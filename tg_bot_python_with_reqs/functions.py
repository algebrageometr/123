import requests
import time
from config import Config
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent

def log_error(msg: str):
    with open(BASE_DIR / "response_tg_errors.txt", "a", encoding="utf-8") as f:
        f.write(f"DATE: {time.strftime('%Y-%m-%d %H:%M:%S')} RESPONSE: {msg}\n")

def send_answer(text: str, chat_id: int, markdown: bool = True):
    url = f"https://api.telegram.org/bot{Config.telegram_bot_token}/sendMessage"
    data = {
        "chat_id": chat_id,
        "text": text
    }
    if markdown:
        data["parse_mode"] = "Markdown"
    try:
        r = requests.post(url, data=data, timeout=10)
        log_error(r.text)
    except Exception as e:
        log_error(str(e))

def get_requisites_epay(data: dict) -> dict:
    try:
        r = requests.post(Config.epay_requisite_url, data=data, timeout=30)
        return r.json()
    except Exception as e:
        log_error(str(e))
        return {}

def handle_success_callback(order_id: int):
    orders_file = BASE_DIR / "orders.txt"
    if not orders_file.exists():
        return
    with open(orders_file, "r", encoding="utf-8") as f:
        rows = f.readlines()
    need_row = None
    for row in rows:
        row_data = row.strip().split("|")
        if len(row_data) >= 2 and int(order_id) == int(row_data[1]):
            need_row = row_data
            break
    if need_row:
        send_answer(f"Получена оплата по заказу {order_id}!", int(need_row[2]))

def check_webhook_is_set() -> bool:
    url = f"https://api.telegram.org/bot{Config.telegram_bot_token}/getWebhookInfo"
    try:
        r = requests.get(url, timeout=30).json()
        return bool(r.get("result", {}).get("url"))
    except Exception as e:
        log_error(str(e))
        return False
