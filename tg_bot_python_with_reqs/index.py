from flask import Flask
from config import Config
from functions import check_webhook_is_set

app = Flask(__name__)

@app.route("/")
def index():
    if not check_webhook_is_set():
        link = f"https://api.telegram.org/bot{Config.telegram_bot_token}/setWebhook?url=https://YOUR_DOMAIN/action/tg"
        return f'<a href="{link}">Активировать бота</a>'
    else:
        return f'Бот активирован! Ссылка на Ваш бот - <a href="{Config.telegram_bot_url}">{Config.telegram_bot_url}</a>'

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
