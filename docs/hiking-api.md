# Matka API dokumentatsioon

## Endpoint

- `GET /api/hiking-subjects`

## Query parameetrid

- `q` - otsing pealkirja, asukoha või kirjelduse järgi
- `difficulty` - `easy`, `medium`, `hard`
- `sort` - `latest` (vaikimisi), `distance_asc`, `distance_desc`, `title_asc`
- `limit` - tagastatavate kirjete arv vahemikus `1..100` (vaikimisi `20`)

## Näide

`/api/hiking-subjects?q=raba&difficulty=easy&sort=distance_asc&limit=10`

## Vastuse struktuur

```json
{
  "meta": {
    "cached_until": "2026-04-08T15:30:00.000000Z",
    "filters": {
      "q": "raba",
      "difficulty": "easy",
      "sort": "distance_asc",
      "limit": 10
    }
  },
  "data": [
    {
      "id": 1,
      "title": "Viru raba ring",
      "image": "https://...",
      "description": "Laudtee ja vaatetorn",
      "location": "Lahemaa",
      "difficulty": "easy",
      "distance_km": 3.5,
      "author": "Liisa",
      "created_at": "2026-04-08T14:00:00.000000Z"
    }
  ]
}
```

## Cache

- API vastused on cache'itud 10 minutiks.
- Kui veebivormist lisatakse uus kirje, tõstetakse cache versiooni ja järgmine API päring ehitatakse uuesti.
