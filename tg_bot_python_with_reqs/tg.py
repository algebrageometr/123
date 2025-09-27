from flask import Flask, request
from config import Config
from functions import send_answer, get_requisites_epay, log_error
import time
from pathlib import Path

app = Flask(__name__)
BASE_DIR = Path(__file__).resolve().parent

@app.route("/action/tg", methods=["POST"])
def tg_webhook():
    try:
        update = request.get_json(force=True)
        chat_id = update["message"]["chat"]["id"]
        message = update["message"].get("text", "")

        if message == "/start":
            send_answer(Config.text_before_enter_price, chat_id)
            return "ok", 200

        data_for_api = {
            "amount": message,
            "merchant_order_id": "optional",
            "api_key": Config.api_secret,
            "notice_url": f"{request.url_root}action/callback"
        }

        send_answer("⌛Ожидаем реквизиты...", chat_id)
        result_order_data = get_requisites_epay(data_for_api)

        if result_order_data.get("error_desc"):
            send_answer("Ошибка! " + result_order_data["error_desc"], chat_id, False)
        else:
            order_data = [
                time.strftime("%Y-%m-%d %H:%M:%S"),
                str(result_order_data["order_id"]),
                str(chat_id),
                str(data_for_api["amount"])
            ]
            with open(BASE_DIR / "orders.txt", "a", encoding="utf-8") as f:
                f.write("|".join(order_data) + "\n")

            receiver_wallet_str = f" *Номер карты для оплаты*: `{result_order_data.get('card_number','')}`"

            if (result_order_data.get("qr_sbp_url") or result_order_data.get("card_form_url")) and not result_order_data.get("card_number"):
                url = result_order_data.get("qr_sbp_url") or result_order_data.get("card_form_url")
                receiver_wallet_str = f" *Ссылка на оплату*: [{url}]({url}) "

            msg = (
                f"{Config.code_symbol_order} *Создан заказ*: №{result_order_data['order_id']}\n\n"
                f"{Config.code_symbol_card}{receiver_wallet_str} \n"
                f"{Config.code_symbol_amount} *Сумма платежа*: `{result_order_data['amount']}` {Config.code_symbol_rub}\n\n"
                f"{Config.code_symbol_timer} *Время на оплату*: {Config.time_minutes_for_payment} мин"
            )
            send_answer(msg, chat_id)

            if Config.text_after_requisites:
                send_answer(Config.text_after_requisites, chat_id)

        return "ok", 200
    except Exception as e:
        log_error(str(e))
        return "error", 200

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
