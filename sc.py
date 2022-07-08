from time import sleep
import keyboard
import pyautogui
import win32api
import win32con
from random import randint
from pyautogui import *

def click(x, y):
    win32api.SetCursorPos((x, y))
    sleep(0.01)

sleep(3)

while not keyboard.is_pressed('q'):
    # pyautogui.position()
    sleep(0.1)
    for x in range((randint(10,120))):
        click(1500, 1078)
        click(1600, 1078)
        sleep(1)

    pyautogui.keyDown('alt')
    pyautogui.press('tab')
    pyautogui.keyUp('alt')
    sleep(1)
