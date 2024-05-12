# NBA API Exposed Resources

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

| Resource     | URI                                                            | Method                 | Filter |
| ------------ | -------------------------------------------------------------- | ---------------------- | ------ |
| Teams        | /team | GET, POST, PUT, DELETE | full_name, nickname, abbreviation, city, state, year_founded, year_active_till, owner, page, page_size, order    |
| Team by ID   | /team/{team_id}                        | GET                    | N/A    |
| Team History | /team/{team_id}/history                | GET                    | match_result    |

| POST           | PUT            | DELETE         |
|----------------|----------------|----------------|
| "team_id":     | "team_id":     | team_id        |
| "full_name":   | "full_name":   |                |
| "abbreviation":| "abbreviation":|                |
| "nickname":    | "nickname":    |                |
| "city":        | "city":        |                |
| "state":       | "state":       |                |
| "year_founded":| "year_founded":|                |
| "owner":       | "owner":       |                |
| "year_active_till":| "year_active_till":|          |


### Players

| Resource      | URI                                                                  | Method                 | Filter |
| ------------- | -------------------------------------------------------------------- | ---------------------- | ------ |
| Players       | /players | GET, POST, PUT, DELETE | first_name, country, birthdate, team_name, order, page, page_size, order    |
| Player by ID  | /players/{player_id}                            | GET                    | N/A    |
| Player Drafts | /players/{player_id}/drafts                     | GET                    | N/A    |

| POST           | PUT            | DELETE         |
|----------------|----------------|----------------|
| "person_id":  |   "person_id":    | person_id
| "first_name":     | "first_name":     |      |
| "last_name":   | "last_name":   |                |
| "birthdate":| "birthdate":|                |
| "school":    | "school":    |                |
| "country":        | "country":        |                |
| "height":       | "height":       |                |
| "weight":| "weight":|                |
| "jersey":       | "jersey":       |                |
| "position":| "position":|          |
| "team_id":    | "team_id":    |                |
| "team_name":        | "team_name":        |                |
| "team_abbreviation":       | "team_abbreviation":       |                |
| "team_code":| "team_code":|                |
| "team_city":       | "team_city":       |                |
| "from_year":| "from_year":|          |
| "to_year":        | "to_year":        |                |
| "draft_year":     | "draft_year":       |             |
| "draft_number":| "draft_number":|                |


### Drafts

| Resource           | URI                                                              | Method                 | Filter |
| ------------------ | ---------------------------------------------------------------- | ---------------------- | ------ |
| Drafts             | /draft | GET, POST, PUT, DELETE |  first_name, last_name, player_name, position, weight, wingspan, standing_reach, hand_lenght, hand_width, standing_vertical_leap, max_vertical_leap, bench_press, page, page_size, order   |
| Draft by ID        | /draft/{draft_id}                        | GET                    | N/A    |
| Draft by Player ID | /draft/{player_id}                       | GET                    | N/A    |
| Draft Seasons      | /draft/{player_id}/season                | GET                    | N/A    |

| POST           | PUT            | DELETE         |
|----------------|----------------|----------------|
| "player_id":  |   "player_id":    | player_id
| "season":     | "season":     |      |
| "first_name":   | "first_name":   |                |
| "last_name":| "last_name":|                |
| "position":    | "position":    |                |
| "weight":        | "weight":        |                |
| "wingspan":       | "wingspan":       |                |
| "standing_reach":| "standing_reach":|                |
| "hand_length":       | "hand_length":       |                |
| "hand_width":| "hand_width":|          |
| "standing_vertical_leap":    | "standing_vertical_leap":    |                |
| "max_vertical_leap":        | "max_vertical_leap":        |                |
| "bench_press":       | "bench_press":       |                |

## Games

| Resource   | URI                                                              | Method                 | Filter |
| ---------- | ---------------------------------------------------------------- | ---------------------- | ------ |
| Games      | /games) | GET, POST, PUT, DELETE | season_id, team_id_home, team_abbreviation_home, team_name_home, game_date, matchup_away, wl_away, pts_away, plus_minus_away, season_type, page, page_size, order    |
| Game by ID | /games/{game_id}                         | GET                    | N/A    |
| Game Teams | /games/{game_id}/teams                   | GET                    | N/A    |

## Composite Resources

| Resource | URI                                                              | Method | Filter |
| -------- | ---------------------------------------------------------------- | ------ | ------ |
| Sports    | /sports                       | GET    | c=[Country]    |
| Shows    | /shows | GET    | N/A    |
| BMI Computation    | /BMI | POST    | N/A    |
