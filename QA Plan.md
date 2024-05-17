# QA Plan

## Description

Welcome! This is a Web service that provides comprehensive access to NBA-related information, including player stats, team details, game schedules, and more. Our goal is to create a robust and efficient API for accessing and managing data related to the National Basketball Association (NBA). Whether you're a developer, analyst, or NBA enthusiast, our service aims to be your central hub for all things NBA-related. Explore our endpoints and discover the wealth of information available at your fingertips!

### Authors

- Muhammad Arsalan Saeed
- Maximus Taube
- David Jorge Sousa
- Valentin Atanasov

## Software

- **Dataset**: [Kaggle Basketball Dataset](https://www.kaggle.com/datasets/wyattowalsh/basketball)
- **Database**: [http://localhost/nba-api/](http://localhost/nba-api/)

## Exposed Resources and Operations

### Teams

| Resource     | URI                     | Method                 | Filter                                                                                                        |
| ------------ | ----------------------- | ---------------------- | ------------------------------------------------------------------------------------------------------------- |
| Teams        | /team                   | GET, POST, PUT, DELETE | full_name, nickname, abbreviation, city, state, year_founded, year_active_till, owner, page, page_size, order |
| Team by ID   | /team/{team_id}         | GET                    | N/A                                                                                                           |
| Team History | /team/{team_id}/history | GET                    | N/A                                                                                                           |

### POST

```
[
  {
    "team_id": 1234567890,
    "full_name": "Kings Might",
    "abbreviation": "KGX",
    "nickname": "Kings",
    "city": "Montreal",
    "state": "Quebec",
    "year_founded": "2024",
    "owner": "Julian Frost",
    "year_active_till": "2024"
  }
]
```

### PUT

```
[
  {
    "team_id": 1234567890,
    "full_name": "Kings Might",
    "abbreviation": "KGX",
    "nickname": "Kings",
    "city": "Montreal",
    "state": "Quebec",
    "year_founded": "2024",
    "owner": "Julian Frost",
    "year_active_till": "2024"
  }
]
```

### DELETE

```
[
  1234567890
]
```

### Players

| Resource      | URI                         | Method                 | Filter                                                                                                                                                                                                                            |
| ------------- | --------------------------- | ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Players       | /players                    | GET, POST, PUT, DELETE | person_id, first_name, last_name, birthdate, school, country, height, weight, jersey, position, team_id, team_name, team_abbreviation, team_code, team_city, from_year, to_year, draft_year, draft_number, order, page, page_size |
| Player by ID  | /players/{player_id}        | GET                    | N/A                                                                                                                                                                                                                               |
| Player Drafts | /players/{player_id}/drafts | GET                    | N/A                                                                                                                                                                                                                               |

| POST                 | PUT                  | DELETE    |
| -------------------- | -------------------- | --------- |
| "person_id":         | "person_id":         | person_id |
| "first_name":        | "first_name":        |           |
| "last_name":         | "last_name":         |           |
| "birthdate":         | "birthdate":         |           |
| "school":            | "school":            |           |
| "country":           | "country":           |           |
| "height":            | "height":            |           |
| "weight":            | "weight":            |           |
| "jersey":            | "jersey":            |           |
| "position":          | "position":          |           |
| "team_id":           | "team_id":           |           |
| "team_name":         | "team_name":         |           |
| "team_abbreviation": | "team_abbreviation": |           |
| "team_code":         | "team_code":         |           |
| "team_city":         | "team_city":         |           |
| "from_year":         | "from_year":         |           |
| "to_year":           | "to_year":           |           |
| "draft_year":        | "draft_year":        |           |
| "draft_number":      | "draft_number":      |           |

#### Example of a player resource

    "person_id": 76001,
    "first_name": "Alaa",
    "last_name": "Abdelnaby",
    "birthdate": "0000-00-00 00:00:00",
    "school": "Duke",
    "country": "USA",
    "height": "6-10",
    "weight": "240",
    "jersey": "30",
    "position": "Forward",
    "team_id": 1610612757,
    "team_name": "Trail Blazers",
    "team_abbreviation": "POR",
    "team_code": "blazers",
    "team_city": "Portland",
    "from_year": 1990,
    "to_year": 1994,
    "draft_year": "1990",
    "draft_number": "25"

### Drafts

| Resource           | URI                       | Method                 | Filter                                                                                                                                                                                  |
| ------------------ | ------------------------- | ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Drafts             | /draft                    | GET, POST, PUT, DELETE | first_name, last_name, player_name, position, weight, wingspan, standing_reach, hand_lenght, hand_width, standing_vertical_leap, max_vertical_leap, bench_press, page, page_size, order |
| Draft by ID        | /draft/{draft_id}         | GET                    | N/A                                                                                                                                                                                     |
| Draft by Player ID | /draft/{player_id}        | GET                    | N/A                                                                                                                                                                                     |
| Draft Seasons      | /draft/{player_id}/season | GET                    | N/A                                                                                                                                                                                     |

| POST                      | PUT                       | DELETE    |
| ------------------------- | ------------------------- | --------- |
| "player_id":              | "player_id":              | player_id |
| "season":                 | "season":                 |           |
| "first_name":             | "first_name":             |           |
| "last_name":              | "last_name":              |           |
| "position":               | "position":               |           |
| "weight":                 | "weight":                 |           |
| "wingspan":               | "wingspan":               |           |
| "standing_reach":         | "standing_reach":         |           |
| "hand_length":            | "hand_length":            |           |
| "hand_width":             | "hand_width":             |           |
| "standing_vertical_leap": | "standing_vertical_leap": |           |
| "max_vertical_leap":      | "max_vertical_leap":      |           |
| "bench_press":            | "bench_press":            |           |

## Examples of Correct Resources for /draft:

POST /draft:

```
[
    {
        "season": 2003,
        "player_id": 2,
        "first_name": "Jordan",
        "last_name": "Michel",
        "player_name": "Jordan Michel",
        "position": "SG",
        "weight": 199,
        "wingspan": 6.5,
        "standing_reach": "8.2",
        "hand_length": 7.5,
        "hand_width": 8.0,
        "standing_vertical_leap": 35.5,
        "max_vertical_leap": 40.0,
        "bench_press": 225
    },

    {
        "season": 2003,
        "player_id": 3,
        "first_name": "David",
        "last_name": "Jorge",
        "player_name": "David Jorge",
        "position": "SG",
        "weight": 184,
        "wingspan": 4.5,
        "standing_reach": "6.2",
        "hand_length": 5.5,
        "hand_width": 7.0,
        "standing_vertical_leap": 23.5,
        "max_vertical_leap": 30.0,
        "bench_press": 200
    }
]
```

PUT /draft:

```
[
    {
        "season": 2003,
        "player_id": 3,
        "first_name": "David",
        "last_name": "Jorge",
        "player_name": "David Jorge",
        "position": "SG",
        "weight": 184,
        "wingspan": 4.5,
        "standing_reach": "6.2",
        "hand_length": 5.5,
        "hand_width": 7.0,
        "standing_vertical_leap": 23.5,
        "max_vertical_leap": 30.0,
        "bench_press": 200
    },

    {
        "season": 2003,
        "player_id": 3,
        "first_name": "David",
        "last_name": "Jorge",
        "player_name": "David Jorge",
        "position": "SG",

    }
]
```

DELETE /draft

```
[
    2, 3
]
```

## Examples of Incorrect Resources for /draft:

POST /draft:

```
[
    {
        "season": ls,
        "player_id": ls,
        "first_name": david123,
        "last_name": 123Jorge,
        "player_name": jor12,
        "position": 123,
        "weight": a,
        "wingspan": 2s,
        "standing_reach": 123a,
        "hand_length": 12d,
        "hand_width": 7.0a,
        "standing_vertical_leap": 23.sd5,
        "max_vertical_leap": 123d,
        "bench_press": 123a
    },

    {
        "season": ls,
        "player_id": ls,
        "first_name": aasd12,
        "last_name": 214as,
        "player_name": 1234sadf,
        "position": 123asd,
        "weight": 123asd,
        "wingspan": 123asd,
        "standing_reach": 123asd,
        "hand_length": 123asd,
        "hand_width": 7.12340a,
        "standing_vertical_leap": 23.sd2135,
        "max_vertical_leap": sad,
        "bench_press": 123s
    }
]
```

PUT /draft

```
[
    {
        "season": 12312a,
        "player_id": 31231a,
        "first_name": 123asd,
        "last_name": 123asdf,
        "player_name": 123ad,
        "position": 124asdf,
        "weight": 123asd,
        "wingspan": 123asdx,
        "standing_reach": "asd12",
        "hand_length": 12243sd,
        "hand_width": 2134dsf,
        "standing_vertical_leap": 1234sd,
        "max_vertical_leap": 123asdfx,
        "bench_press": 21ss
    },

    {
        "season": 22sd,
        "player_id": 3sda2,
        "first_name": 1234d,
        "last_name": 123sds,
        "player_name": 123ws,
        "position": 123s,

    }
]
```

DELETE /draft

```
[

]


(or)


[
    21312411
]
```

## Games

| Resource   | URI                    | Method                 | Filter                                                                                                                                                            |
| ---------- | ---------------------- | ---------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Games      | /games)                | GET, POST, PUT, DELETE | season_id, team_id_home, team_abbreviation_home, team_name_home, game_date, matchup_away, wl_away, pts_away, plus_minus_away, season_type, page, page_size, order |
| Game by ID | /games/{game_id}       | GET                    | N/A                                                                                                                                                               |
| Game Teams | /games/{game_id}/teams | GET                    | N/A                                                                                                                                                               |

| POST                      | PUT                       | DELETE  |
| ------------------------- | ------------------------- | ------- |
| "season_id":              | "season_id":              | game_id |
| "team_id_home":           | "team_id_home":           |         |
| "team_abbreviation_home": | "team_abbreviation_home": |         |
| "team_name_home":         | "team_name_home":         |         |
| "game_id":                | "game_id":                |         |
| "game_date":              | "game_date":              |         |
| "pts_home":               | "pts_home":               |         |
| "plus_minus_home":        | "plus_minus_home":        |         |
| "team_id_away":           | "team_id_away":           |         |
| "team_abbreviation_away": | "team_abbreviation_away": |         |
| "team_name_away":         | "team_name_away":         |         |
| "matchup_away":           | "matchup_away":           |         |
| "wl_away":                | "wl_away":                |         |
| "pts_away":               | "pts_away":               |         |
| "plus_minus_away":        | "plus_minus_away":        |         |
| "season_type":            | "season_type":            |         |

## Examples of Correct Resources for /games:

POST /games:

```
[
    {
        "season_id": 12005,
        "team_id_home": 1610612764,
        "team_abbreviation_home": "WAS",
        "team_name_home": "Washington Wizards",
        "game_id": 1,
        "game_date": "2005-10-10 00:00:00",
        "pts_home": "94.0",
        "plus_minus_home": "-22",
        "team_id_away": 1610612739,
        "team_abbreviation_away": "CLE",
        "team_name_away": "Cleveland Cavaliers",
        "matchup_away": "CLE @ WAS",
        "wl_away": "W",
        "pts_away": "116.0",
        "plus_minus_away": "22",
        "season_type": "Pre Season"
    },

    {
        "season_id": 12005,
        "team_id_home": 1610612755,
        "team_abbreviation_home": "PHI",
        "team_name_home": "Philadelphia 76ers",
        "game_id": 2,
        "game_date": "2005-10-11 00:00:00",
        "pts_home": "91.0",
        "plus_minus_home": "-14",
        "team_id_away": 1610612745,
        "team_abbreviation_away": "HOU",
        "team_name_away": "Houston Rockets",
        "matchup_away": "HOU @ PHI",
        "wl_away": "W",
        "pts_away": "105.0",
        "plus_minus_away": "14",
        "season_type": "Pre Season"
    }
]
```

PUT /games:

```
[
    {
        "season_id": 123,
        "team_id_home": 123456,
        "team_abbreviation_home": "WAS",
        "team_name_home": "Washington Wizards",
        "game_id": 1,
        "game_date": "2005-10-10 00:00:00",
        "pts_home": "94.0",
        "plus_minus_home": "-22",
        "team_id_away": 1610612739,
        "team_abbreviation_away": "CLE",
        "team_name_away": "Cleveland Cavaliers",
        "matchup_away": "CLE @ WAS",
        "wl_away": "W",
        "pts_away": "116.0",
        "plus_minus_away": "22",
        "season_type": "Pre Season"
    },

    {
        "season_id": 123,
        "team_id_home": 1610612755,
        "team_abbreviation_home": "MTL",
        "team_name_home": "Montreal Warriors",
        "game_id": 2
    }
]
```

DELETE /games

```
[
    1, 2
]
```

## Examples of Incorrect Resources for /games:

POST /games:

```
[
    {
        "game_id": 10500001,
        "game_date": "2005-10-10 00:00:00",
        "pts_home": "94.0",
        "plus_minus_home": "-22",
        "team_id_away": 1610612739,
        "team_abbreviation_away": "CLE",
        "team_name_away": "Cleveland Cavaliers",
        "matchup_away": "CLE @ WAS",
        "wl_away": "W",
        "pts_away": "116.0",
        "plus_minus_away": "22",
        "season_type": "Pre Season"
    },

    {
        "season_id": "abcdefghigklmnopqestuvwxyz",
        "team_id_home": 1610612755,
        "team_abbreviation_home": "PHI",
        "team_name_home": "Philadelphia 76ers",
        "game_id": 2,
        "game_date": "2005-10-11 00:00:00",
        "pts_home": "91.0",
        "plus_minus_home": "-14",
        "team_id_away": 1610612745,
        "team_abbreviation_away": "HOU",
        "team_name_away": "Houston Rockets",
        "matchup_away": "HOU @ PHI",
        "wl_away": "W",
        "pts_away": "105.0",
        "plus_minus_away": "14",
        "season_type": "Pre Season"
    }
]
```

PUT /games

```
[
    {
        "season_id": 12005,
        "team_id_home": 1610612764,
        "team_abbreviation_home": "WAS",
        "team_name_home": "Washington Wizards",
        "game_date": "2005-10-10 00:00:00",
        "pts_home": "94.0"
    },

    {
        "season_id": 12005,
        "team_id_home": "abcdefghigklmnopqestuvwxyz",
        "team_abbreviation_home": "PHI",
        "team_name_home": "Philadelphia 76ers",
        "game_id": 2
    }
]
```

DELETE /games

```
[

]


(or)


[
    100000000000000000
]
```

## Composite Resources

| Resource        | URI     | Method | Filter      |
| --------------- | ------- | ------ | ----------- |
| Sports          | /sports | GET    | c=[Country] |
| Shows           | /shows  | GET    | N/A         |
| BMI Computation | /BMI    | POST   | N/A         |

## BMI

| POST      |
| --------- |
| "height": |
| "weight": |
| "unit":   |
| "gender": |
