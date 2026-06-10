# -*- coding: utf-8 -*-
"""
Download /var/www/r1riepas via SSH tar (no git on server),
commit and force-push as server baseline. Server: no pull/push/git changes.
"""
import io
import os
import shutil
import subprocess
import sys
import tarfile
import tempfile
from pathlib import Path

import paramiko

HOST = "87.99.76.51"
PORT = 22000
USER = "r1riepas"
PASSWORD = "riepas123"
REMOTE_BASE = "/var/www/r1riepas"
REPO = "https://github.com/TrixxDev/r1original.git"
WORK = Path(r"E:\r1riepasclone\_server_snapshot_work")

EXCLUDES = [
    ".git",
    ".env",
    ".env.save",
    ".env.docker",
    "db_access.txt",
    "debug-06f91d.log",
    "debug-070f40.log",
    "node_modules",
    "vendor",
]


def run_git(args, cwd):
    print("+", " ".join(args))
    subprocess.run(args, cwd=cwd, check=True)


def main():
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    if WORK.exists():
        shutil.rmtree(WORK)
    WORK.mkdir(parents=True)

    exclude_flags = " ".join(f"--exclude={x}" for x in EXCLUDES)
    tar_cmd = f"cd {REMOTE_BASE} && tar -czf - {exclude_flags} ."

    print("Downloading server files (read-only tar over SSH)...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(HOST, port=PORT, username=USER, password=PASSWORD, timeout=30)
    _, stdout, stderr = client.exec_command(tar_cmd, timeout=1800)
    data = stdout.read()
    err = stderr.read().decode("utf-8", errors="replace").strip()
    code = stdout.channel.recv_exit_status()
    client.close()
    if code != 0:
        print("tar failed:", err)
        sys.exit(code)
    print(f"Downloaded {len(data) / 1024 / 1024:.1f} MB")

    print("Extracting...")
    with tarfile.open(fileobj=io.BytesIO(data), mode="r:gz") as tf:
        tf.extractall(WORK)

    run_git(["git", "init", "-q"], WORK)
    run_git(["git", "branch", "-M", "master"], WORK)
    run_git(
        [
            "git",
            "-c",
            "user.name=r1riepas-server",
            "-c",
            "user.email=server@r1riepas.lv",
            "add",
            "-A",
        ],
        WORK,
    )
    run_git(
        [
            "git",
            "-c",
            "user.name=r1riepas-server",
            "-c",
            "user.email=server@r1riepas.lv",
            "commit",
            "-m",
            "Server production baseline snapshot.",
        ],
        WORK,
    )
    run_git(["git", "remote", "add", "origin", REPO], WORK)
    run_git(["git", "push", "-f", "origin", "master"], WORK)

    sha = subprocess.check_output(["git", "rev-parse", "HEAD"], cwd=WORK, text=True).strip()
    print(f"\nDONE: server baseline on GitHub master = {sha}")
    print("Server was not modified (no pull/push/git on server).")


if __name__ == "__main__":
    main()
