# 🎥 AI Video Analytics Backend

Laravel backend для системы анализа видео с использованием AI/ML микросервиса FastAPI.

## 📋 Архитектура

```
Laravel Backend (PHP)  ←→  FastAPI Microservice (Python)
     ↓                         ↓
  PostgreSQL               YOLOv8 + DeepSORT
     ↓                         ↓
   Redis                   AI Reports
```

## 🚀 Быстрый старт

### 1. Установка зависимостей
```bash
composer install
npm install
```

### 2. Настройка .env
```env
APP_NAME="AI Video Analytics Backend"
APP_ENV=local
APP_KEY=

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=video_analytics
DB_USERNAME=your_username
DB_PASSWORD=your_password

# FastAPI Integration
FASTAPI_BASE_URL=http://localhost:8090
FASTAPI_API_KEY=my-super-secret-key-123

# FastAPI HTTP client settings
# Timeout (seconds), number of retries, and sleep between retries (ms)
FASTAPI_TIMEOUT=10
FASTAPI_RETRIES=2
FASTAPI_RETRY_SLEEP_MS=200

# Telegram (optional)
TELEGRAM_BOT_TOKEN=your-bot-token
TELEGRAM_CHAT_ID=your-chat-id
```

### 3. Миграции
```bash
php artisan migrate
```

### 4. Запуск
```bash
php artisan serve
```

## 📡 API Endpoints

### Авторизация

#### `POST /api/auth/register`
Регистрация нового пользователя

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "user": {...},
  "token": "1|..."
}
```

#### `POST /api/auth/login`
Вход в систему

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### `GET /api/auth/me`
Получить текущего пользователя

**Headers:**
```
Authorization: Bearer {token}
```

#### `POST /api/auth/logout`
Выход из системы

### Видео Сессии

#### `GET /api/sessions`
Список всех сессий пользователя

#### `POST /api/sessions`
Создать новую сессию

**Request:**
```json
{
  "name": "My Video",
  "source_type": "file",
  "source_path": "/path/to/video.mp4",
  "duration": 60,
  "confidence_threshold": 0.5
}
```

#### `GET /api/sessions/{id}`
Получить детали сессии

#### `DELETE /api/sessions/{id}`
Удалить сессию

#### `POST /api/sessions/{id}/start-analysis`
Запустить анализ видео (вызывает FastAPI)

#### `GET /api/sessions/{id}/status`
Получить статус анализа

### Отчеты

#### `GET /api/reports`
Список всех отчетов

#### `POST /api/reports`
Создать отчет

**Request:**
```json
{
  "session_id": 1,
  "report_type": "summary"
}
```

#### `GET /api/reports/{id}`
Получить отчет

#### `GET /api/reports/{id}/analytics`
Получить аналитику

#### `GET /api/reports/{id}/heatmap`
Получить тепловую карту

#### `GET /api/reports/{id}/summary`
Получить краткое резюме

### Админ

#### `GET /api/admin/sessions`
Все сессии (админ вид)

#### `GET /api/admin/users`
Список пользователей

#### `GET /api/admin/users/{id}`
Детали пользователя

#### `GET /api/admin/settings`
Настройки системы

## 🔧 Структура проекта

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # User API контроллеры
│   │   └── Admin/        # Admin контроллеры
│   ├── Requests/         # Form Requests
│   └── Traits/           # ApiResponseHelper, etc
├── Models/               # Eloquent модели
├── Services/             # Бизнес-логика
│   ├── FastApiClient.php
│   ├── VideoAnalysisService.php
│   └── ReportGenerationService.php
├── DTOs/                 # Data Transfer Objects
└── Events/               # Events & Listeners
```

## 🔌 Интеграция с FastAPI

Backend интегрируется с FastAPI микросервисом через `FastApiClient`:

```php
$client = app(FastApiClient::class);
$response = $client->startAnalysis([
    'source_type' => 'file',
    'source_path' => '/path/to/video.mp4',
]);
```

## 📊 База данных

### Таблицы:
- `video_sessions` - Видео сессии
- `analysis_reports` - Отчеты анализа
- `detections` - Детекции объектов
- `notifications` - Уведомления

## 🎯 Технологии

- **Laravel 12** - PHP Framework
- **Sanctum** - API Authentication
- **PostgreSQL** - Database
- **Redis** - Cache/Queue
- **FastAPI** - AI Microservice

## 📝 TODO

- [ ] Jobs для фоновой обработки
- [ ] Events и Listeners
- [ ] Telegram уведомления
- [ ] WebSockets для real-time
- [ ] Swagger документация
- [ ] Unit/Integration тесты

## 🤝 Разработка

```bash
# Разработка
npm run dev

# Тесты
php artisan test

# Миграции
php artisan migrate
php artisan migrate:rollback
```

## 🧪 Postman

- Коллекция: `docs/postman/Video-Analytics-back.postman_collection.json`
- Окружение (опционально): `docs/postman/Video-Analytics.postman_environment.json`

Шаги:
- Импортируйте коллекцию и (при необходимости) окружение в Postman.
- В окружении задайте переменные:
  - `apiUrl` = `http://localhost:8000`
  - `token` = ваш Bearer токен (после логина)
  - `sessionId`, `sessionDbId` — при необходимости для запросов по сессии
- Выберите окружение и запускайте запросы.

Примечания:
- Если в коллекции встречаются захардкоженные токены/ID — замените их на переменные `{{token}}`, `{{sessionId}}` или используйте окружение.


## 👤 Автор

Umid Urinov — Backend Engineer (Laravel, High-Load, Integrations)

📬 Связь:
Telegram: **@uumid82**  
Email: **umidurinov14@gmail.com**

Если вы хотите использовать проект в продакшене, задать вопрос  
или обсудить сотрудничество — смело пишите.


