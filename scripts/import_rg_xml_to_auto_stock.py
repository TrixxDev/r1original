#!/usr/bin/env python3
"""
Импорт остатков из rg_xml.txt (выгрузка tyres) в таблицу auto_stock.

Для каждой <tyre>:
  article  = текст <id>
  quantity = число из <qty><stock>
  tire_id  = NULL
  itype    = константа (по умолчанию rg), чтобы не пересечься с i3/gy/rz

Повторный запуск: обновляет quantity у строк с тем же article + itype + tire_id IS NULL,
  иначе вставляет новую (идемпотентно без дублей для «висячих» складских строк).

  pip install -r requirements-db.txt
  py import_rg_xml_to_auto_stock.py --dry-run
  py import_rg_xml_to_auto_stock.py
  py import_rg_xml_to_auto_stock.py --xml ..\\rg_xml.txt --itype rg
"""

from __future__ import annotations

import argparse
import re
import sys
import xml.etree.ElementTree as ET
from pathlib import Path


def load_db_access(path: Path) -> dict[str, str]:
    text = path.read_text(encoding="utf-8", errors="replace")
    cfg: dict[str, str] = {}
    for raw in text.splitlines():
        line = raw.strip()
        if not line or line.startswith("#"):
            continue
        m = re.match(r"^(\w+)\s*:\s*(.*)$", line)
        if not m:
            continue
        key, val = m.group(1).lower(), m.group(2).strip()
        cfg[key] = val
    required = ("host", "user", "pass", "db")
    missing = [k for k in required if k not in cfg]
    if missing:
        raise SystemExit(f"В {path} не хватает полей: {', '.join(missing)}")
    if "port" not in cfg:
        cfg["port"] = "3306"
    return cfg


def tyre_row(elem: ET.Element) -> tuple[str, float] | None:
    """Возвращает (article, quantity) или None, если данных нет."""
    id_el = elem.find("id")
    qty_el = elem.find("qty")
    if id_el is None or id_el.text is None or not str(id_el.text).strip():
        return None
    stock_el = qty_el.find("stock") if qty_el is not None else None
    if stock_el is None or stock_el.text is None or not str(stock_el.text).strip():
        return None
    article = str(id_el.text).strip()
    try:
        quantity = float(str(stock_el.text).strip().replace(",", "."))
    except ValueError:
        return None
    return article, quantity


def iter_tyres(xml_path: Path):
    """Потоковый разбор: по одному закрытому элементу tyre."""
    for _event, elem in ET.iterparse(str(xml_path), events=("end",)):
        if elem.tag != "tyre":
            continue
        row = tyre_row(elem)
        elem.clear()
        if row is not None:
            yield row


def upsert_stock(
    cur,
    article: str,
    quantity: float,
    itype: str,
) -> str:
    """insert | update для строк без привязанного tire_id."""
    cur.execute(
        """
        SELECT stock_id FROM auto_stock
        WHERE article = %s AND itype = %s AND tire_id IS NULL
        LIMIT 1
        """,
        (article, itype),
    )
    found = cur.fetchone()
    if found:
        sid = found["stock_id"] if isinstance(found, dict) else found[0]
        cur.execute(
            """
            UPDATE auto_stock
            SET quantity = %s, updated_at = CURRENT_TIMESTAMP
            WHERE stock_id = %s
            """,
            (quantity, sid),
        )
        return "updated"
    cur.execute(
        """
        INSERT INTO auto_stock (tire_id, article, quantity, itype)
        VALUES (NULL, %s, %s, %s)
        """,
        (article, quantity, itype),
    )
    return "inserted"


def main() -> None:
    script_dir = Path(__file__).resolve().parent
    default_xml = script_dir.parent / "rg_xml.txt"
    default_access = script_dir.parent / "db_access.txt"

    ap = argparse.ArgumentParser(description="Импорт rg_xml.txt → auto_stock")
    ap.add_argument("--xml", type=Path, default=default_xml, help="Путь к rg_xml.txt")
    ap.add_argument("--access", type=Path, default=default_access, help="db_access.txt")
    ap.add_argument(
        "--itype",
        default="rg",
        help="Значение itype для этой выгрузки (по умолчанию rg)",
    )
    ap.add_argument(
        "--dry-run",
        action="store_true",
        help="Только разбор XML, без записи в БД",
    )
    ap.add_argument("--limit", type=int, default=0, help="Обработать только N шин (0 = все)")
    args = ap.parse_args()

    if not args.xml.is_file():
        raise SystemExit(f"Файл не найден: {args.xml}")

    itype = (args.itype or "rg").strip()[:10]
    if not itype:
        raise SystemExit("itype не может быть пустым")

    processed = 0
    skipped = 0
    inserted = 0
    updated = 0

    if args.dry_run:
        for article, qty in iter_tyres(args.xml):
            if args.limit and processed >= args.limit:
                break
            processed += 1
            if processed <= 3:
                print(f"  sample: article={article!r} quantity={qty}")
        print(f"[dry-run] разобрано записей: {processed}")
        return

    try:
        import pymysql
    except ImportError:
        print("Установите: pip install pymysql", file=sys.stderr)
        raise SystemExit(1) from None

    cfg = load_db_access(args.access)
    conn = pymysql.connect(
        host=cfg["host"],
        port=int(cfg["port"]),
        user=cfg["user"],
        password=cfg["pass"],
        database=cfg["db"],
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )

    with conn:
        with conn.cursor() as cur:
            for article, qty in iter_tyres(args.xml):
                if args.limit and processed >= args.limit:
                    break
                if len(article) > 100:
                    skipped += 1
                    continue
                action = upsert_stock(cur, article, qty, itype)
                processed += 1
                if action == "inserted":
                    inserted += 1
                else:
                    updated += 1
                if processed % 2000 == 0:
                    conn.commit()
                    print(f"  … {processed} строк", flush=True)
            conn.commit()

    print(
        f"Готово: обработано={processed}, вставлено={inserted}, обновлено={updated}, "
        f"пропущено (длинный article)={skipped}, itype={itype!r}"
    )


if __name__ == "__main__":
    main()
