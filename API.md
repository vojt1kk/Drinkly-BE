# Drinkly API Documentation

Base URL: `http://drinkly.test/api/v1` (nebo `https://your-domain.com/api/v1` v produkci)

## Authentication

API používá **Laravel Sanctum** pro autentizaci. Po úspěšném přihlášení nebo registraci obdržíš **Bearer token**, který musíš posílat v každém chráněném requestu.

### Headers pro chráněné endpointy:
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

---

## Public Endpoints (bez autentizace)

### 1. Register User

Vytvoří nového uživatele a vrátí autentizační token.

**Endpoint:** `POST /register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:** `201 Created`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-03T14:20:38.000000Z",
    "updated_at": "2025-12-03T14:20:38.000000Z"
  },
  "token": "1|ljzluaR2YLuYZ06x5xQXq7Hsq67d7Vfqt7kaXcPV17bc53c1"
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "Uživatel s tímto e-mailem už existuje. Přihlas se, nebo použij jiný e-mail."
    ]
  }
}
```

---

### 2. Login User

Přihlásí existujícího uživatele a vrátí autentizační token.

**Endpoint:** `POST /login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-03T14:20:38.000000Z",
    "updated_at": "2025-12-03T14:20:38.000000Z"
  },
  "token": "2|abc123def456..."
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": [
      "The provided credentials are incorrect."
    ]
  }
}
```

---

## Protected Endpoints (vyžadují autentizaci)

Všechny následující endpointy vyžadují **Bearer token** v `Authorization` headeru.

### 3. Get Current User

Vrátí informace o aktuálně přihlášeném uživateli.

**Endpoint:** `GET /user`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-03T14:20:38.000000Z",
    "updated_at": "2025-12-03T14:20:38.000000Z"
  }
}
```

**Error Response:** `401 Unauthorized`
```json
{
  "message": "Unauthenticated."
}
```

---

### 4. Logout User

Odhlásí uživatele a smaže všechny jeho tokeny.

**Endpoint:** `POST /logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:** `200 OK`
```json
{
  "message": "Logged out successfully"
}
```

---

## Water Intake Endpoints

### 5. List Water Intakes

Vrátí seznam záznamů o příjmu vody pro přihlášeného uživatele.

**Endpoint:** `GET /water-intake`

**Query Parameters:**
- `date` (optional): Filtr podle konkrétního data ve formátu `YYYY-MM-DD`. Pokud není zadáno, vrací všechny záznamy.

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:** `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "amount": 250,
      "intake_time": "2025-12-03T14:30:00.000000Z"
    },
    {
      "id": 2,
      "amount": 300,
      "intake_time": "2025-12-03T15:00:00.000000Z"
    }
  ],
  "meta": {
    "date": "2025-12-03",
    "total_amount": 550
  }
}
```

**Příklad s filtrem:**
```
GET /water-intake?date=2025-12-03
```

Vrátí pouze záznamy za 3. prosince 2025 a `meta.total_amount` bude součet pouze za tento den.

---

### 6. Store Water Intake

Vytvoří nový záznam o příjmu vody.

**Endpoint:** `POST /water-intake`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": 250,
  "intake_time": "2025-12-03T14:30:00Z"
}
```

**Pole:**
- `amount` (required): Množství vody v mililitrech (integer, min: 1)
- `intake_time` (optional): Čas, kdy byla voda vypita (ISO 8601 format). Pokud není zadáno, použije se aktuální čas (`now()`).

**Response:** `201 Created`
```json
{
  "data": {
    "id": 1,
    "amount": 250,
    "intake_time": "2025-12-03T14:30:00.000000Z"
  }
}
```

**Error Response:** `422 Unprocessable Entity`
```json
{
  "message": "The amount field must be at least 1.",
  "errors": {
    "amount": [
      "The amount field must be at least 1."
    ]
  }
}
```

---

### 7. Get Weekly Statistics

Vrátí týdenní statistiku příjmu vody s denním rozpisem.

**Endpoint:** `GET /water-intake/weekly-stats`

**Query Parameters:**
- `start_date` (optional): Začátek týdne ve formátu `YYYY-MM-DD`. Pokud není zadáno, použije se aktuální týden (od pondělí).

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:** `200 OK`
```json
{
  "week_start": "2025-12-01",
  "week_end": "2025-12-07",
  "total_amount": 1750,
  "daily_breakdown": {
    "2025-12-01": 200,
    "2025-12-02": 300,
    "2025-12-03": 500,
    "2025-12-04": 250,
    "2025-12-05": 300,
    "2025-12-06": 0,
    "2025-12-07": 200
  }
}
```

**Příklad s vlastním datem:**
```
GET /water-intake/weekly-stats?start_date=2025-11-25
```

Vrátí týden od 25. listopadu 2025 (pondělí) do 1. prosince 2025 (neděle).

---

## Error Responses

Všechny endpointy mohou vrátit následující chybové kódy:

### 401 Unauthorized
Když chybí nebo je neplatný autentizační token.

```json
{
  "message": "Unauthenticated."
}
```

### 422 Unprocessable Entity
Když validace requestu selže.

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message"
    ]
  }
}
```

### 500 Internal Server Error
Když dojde k neočekávané chybě na serveru.

---

## Data Types

### DateTime Format
Všechny datumy a časy jsou v **ISO 8601** formátu:
- `2025-12-03T14:30:00.000000Z`
- `2025-12-03T14:30:00Z`

### Amount
Množství vody je vždy v **mililitrech** (ml) jako integer.

---

## Example Flow

### 1. Registrace nového uživatele
```bash
POST /api/v1/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Response: { "user": {...}, "token": "1|abc123..." }
```

### 2. Přidání záznamu vody
```bash
POST /api/v1/water-intake
Authorization: Bearer 1|abc123...
{
  "amount": 250
}

# Response: { "data": { "id": 1, "amount": 250, ... } }
```

### 3. Získání denního součtu
```bash
GET /api/v1/water-intake
Authorization: Bearer 1|abc123...

# Response: { "data": [...], "meta": { "date": "2025-12-03", "total_amount": 500 } }
```

### 4. Získání týdenní statistiky
```bash
GET /api/v1/water-intake/weekly-stats
Authorization: Bearer 1|abc123...

# Response: { "week_start": "...", "total_amount": 1750, "daily_breakdown": {...} }
```

---

## Notes

- **Denní reset**: Každý den se součet (`total_amount`) automaticky resetuje na 0. Záznamy z včerejška se nepočítají do dnešního součtu.
- **Automatický čas**: Pokud nepošleš `intake_time` při vytváření záznamu, automaticky se použije aktuální čas (`now()`).
- **Týdenní statistika**: Týden začíná v pondělí a končí v neděli. Dny bez záznamů mají hodnotu `0` v `daily_breakdown`.

