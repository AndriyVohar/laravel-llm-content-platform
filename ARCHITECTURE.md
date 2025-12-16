# Architecture: Laravel + Python AI Service

## Огляд архітектури

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT (Browser)                               │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         LARAVEL APPLICATION                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │ Controllers │  │   Models    │  │   Queues    │  │  AIServiceClient    │ │
│  │  (Inertia)  │  │  (Eloquent) │  │   (Redis)   │  │  (HTTP to Python)   │ │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      │ HTTP/REST
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                       PYTHON AI SERVICE (FastAPI)                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐ │
│  │   Routes    │  │   Agents    │  │    Tools    │  │   LLM Providers     │ │
│  │  /chat      │  │  ChatAgent  │  │ WebSearch   │  │  - DeepInfra        │ │
│  │  /generate  │  │  TaskAgent  │  │ Calculator  │  │  - OpenAI           │ │
│  │  /tools     │  │  RAGAgent   │  │ Database    │  │  - Ollama           │ │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      │ API Calls
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           LLM PROVIDERS                                     │
│     DeepInfra    │      OpenAI       │      Ollama      │     Anthropic     │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Компоненти

### 1. Laravel Application (PHP)
- **Відповідальність**: Web UI, автентифікація, бізнес-логіка, база даних
- **Комунікація з AI**: HTTP клієнт до Python сервісу
- **Черги**: Для async AI задач (генерація контенту)

### 2. Python AI Service (FastAPI)
- **Відповідальність**: AI логіка, tool calling, prompt engineering
- **Переваги**: 
  - Краща AI екосистема (langchain, openai SDK)
  - Легко перевикористати в інших проектах
  - Незалежне масштабування

### 3. Tool Calling Flow

```
Laravel Request
      │
      ▼
Python AI Service
      │
      ▼
┌─────────────────────────────────────┐
│         LLM Request #1              │
│   (prompt + available tools)        │
└─────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────┐
│         LLM Response                │
│   tool_calls: [web_search, ...]     │
└─────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────┐
│       Execute Tools                 │
│   - Call web_search()               │
│   - Get results                     │
└─────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────┐
│         LLM Request #2              │
│   (original prompt + tool results)  │
└─────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────┐
│       Final Response                │
│   → Return to Laravel               │
└─────────────────────────────────────┘
```

## Структура Python AI Service

```
python-ai-service/
├── app/
│   ├── __init__.py
│   ├── main.py                 # FastAPI app
│   ├── config.py               # Settings
│   ├── routers/
│   │   ├── __init__.py
│   │   ├── chat.py             # Chat endpoints
│   │   └── tools.py            # Tool management
│   ├── services/
│   │   ├── __init__.py
│   │   ├── llm_service.py      # LLM abstraction
│   │   └── tool_executor.py    # Tool calling loop
│   ├── providers/
│   │   ├── __init__.py
│   │   ├── base.py             # Base LLM provider
│   │   ├── deepinfra.py        # DeepInfra
│   │   ├── openai_provider.py  # OpenAI
│   │   └── ollama.py           # Ollama
│   ├── tools/
│   │   ├── __init__.py
│   │   ├── base.py             # Base tool class
│   │   ├── web_search.py
│   │   ├── calculator.py
│   │   └── database.py
│   └── schemas/
│       ├── __init__.py
│       ├── chat.py             # Request/Response models
│       └── tools.py
├── tests/
├── requirements.txt
├── Dockerfile
└── docker-compose.yml
```

## API Endpoints (Python Service)

### POST /api/chat
```json
{
  "messages": [
    {"role": "user", "content": "Знайди інформацію про Laravel 11"}
  ],
  "provider": "deepinfra",
  "model": "meta-llama/Llama-3.3-70B-Instruct-Turbo",
  "tools": ["web_search", "calculator"],
  "stream": false
}
```

### Response
```json
{
  "success": true,
  "message": {
    "role": "assistant",
    "content": "Laravel 11 вийшов у березні 2024..."
  },
  "tool_calls_made": [
    {"tool": "web_search", "query": "Laravel 11 release"}
  ],
  "usage": {
    "prompt_tokens": 150,
    "completion_tokens": 200,
    "total_tokens": 350
  },
  "provider": "deepinfra",
  "model": "meta-llama/Llama-3.3-70B-Instruct-Turbo"
}
```

### GET /api/tools
Список доступних tools

### GET /api/providers
Список доступних LLM провайдерів

### GET /api/health
Health check

## Laravel Integration

```php
// app/Services/AIServiceClient.php
class AIServiceClient
{
    public function chat(array $messages, array $options = []): array
    {
        return Http::post(config('ai.service_url') . '/api/chat', [
            'messages' => $messages,
            'provider' => $options['provider'] ?? 'deepinfra',
            'model' => $options['model'] ?? null,
            'tools' => $options['tools'] ?? [],
        ])->json();
    }
}
```

## Переваги цієї архітектури

1. **Reusable**: Python сервіс можна використати з будь-якого проекту
2. **Scalable**: Окреме масштабування AI сервісу
3. **Flexible**: Легко додати нові LLM провайдери та tools
4. **Testable**: Кожен компонент тестується окремо
5. **Provider Agnostic**: Легко переключатись між DeepInfra/OpenAI/Ollama

## Docker Setup

```yaml
# docker-compose.yml
services:
  laravel:
    build: ./docker/php
    depends_on:
      - ai-service
    environment:
      AI_SERVICE_URL: http://ai-service:8000
      
  ai-service:
    build: ./python-ai-service
    ports:
      - "8000:8000"
    environment:
      DEEPINFRA_API_KEY: ${DEEPINFRA_API_KEY}
      OPENAI_API_KEY: ${OPENAI_API_KEY}
```

## Наступні кроки

1. [ ] Створити базову структуру Python AI Service
2. [ ] Реалізувати DeepInfra провайдер
3. [ ] Реалізувати tool calling loop
4. [ ] Додати базові tools (web_search, calculator)
5. [ ] Інтегрувати з Laravel через AIServiceClient
6. [ ] Налаштувати Docker для обох сервісів
