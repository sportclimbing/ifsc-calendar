## IFSC API Endpoints

Some IFSC API endpoints with example responses.

#### Authentication
Some endpoints require authorization. Here's how to do that:
 - Fetch headers from `https://ifsc.results.info`
 - Fetch a cookie called `_verticallife_resultservice_session` from said headers
 - Make a request to a protected API endpoint sending along this session cookie
 - Add a `Referer` header with the value `https://ifsc.results.info/`


### League Information
```http request
GET https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=184
```
<details>
  <summary>Show Response</summary>

```json
{
  "events": [
    {
      "event": "UIAA Worldcup - Imst (AUT) 2003",
      "event_id": 307,
      "url": "/api/v1/events/307",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-05-22 00:00:00 UTC",
      "ends_at": "2003-05-23 00:00:00 UTC",
      "local_start_date": "2003-05-22",
      "local_end_date": "2003-05-23",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Yekaterinburg (RUS) 2003 (B+S)",
      "event_id": 308,
      "url": "/api/v1/events/308",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-05-30 00:00:00 UTC",
      "ends_at": "2003-06-01 00:00:00 UTC",
      "local_start_date": "2003-05-30",
      "local_end_date": "2003-06-01",
      "timezone": null,
      "d_cats": [
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        },
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Yekaterinburg (RUS) 2003 (S)",
      "event_id": 309,
      "url": "/api/v1/events/309",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-04 00:00:00 UTC",
      "ends_at": "2003-06-04 00:00:00 UTC",
      "local_start_date": "2003-06-04",
      "local_end_date": "2003-06-04",
      "timezone": null,
      "d_cats": [
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Yekaterinburg (RUS) 2003 (D+S)",
      "event_id": 310,
      "url": "/api/v1/events/310",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-06 00:00:00 UTC",
      "ends_at": "2003-06-08 00:00:00 UTC",
      "local_start_date": "2003-06-06",
      "local_end_date": "2003-06-08",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        },
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Fiera di Primiero (ITA) 2003",
      "event_id": 311,
      "url": "/api/v1/events/311",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-13 00:00:00 UTC",
      "ends_at": "2003-06-15 00:00:00 UTC",
      "local_start_date": "2003-06-13",
      "local_end_date": "2003-06-15",
      "timezone": null,
      "d_cats": [
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Lecco (ITA) 2003 (speed)",
      "event_id": 312,
      "url": "/api/v1/events/312",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-23 00:00:00 UTC",
      "ends_at": "2003-06-23 00:00:00 UTC",
      "local_start_date": "2003-06-23",
      "local_end_date": "2003-06-23",
      "timezone": null,
      "d_cats": [
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Lecco (ITA) 2003 (boulder)",
      "event_id": 313,
      "url": "/api/v1/events/313",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-24 00:00:00 UTC",
      "ends_at": "2003-06-25 00:00:00 UTC",
      "local_start_date": "2003-06-24",
      "local_end_date": "2003-06-25",
      "timezone": null,
      "d_cats": [
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Lecco (ITA) 2003 (difficulty)",
      "event_id": 314,
      "url": "/api/v1/events/314",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-06-27 00:00:00 UTC",
      "ends_at": "2003-06-28 00:00:00 UTC",
      "local_start_date": "2003-06-27",
      "local_end_date": "2003-06-28",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldchampionship - Chamonix (FRA) 2003",
      "event_id": 315,
      "url": "/api/v1/events/315",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-07-09 00:00:00 UTC",
      "ends_at": "2003-07-13 00:00:00 UTC",
      "local_start_date": "2003-07-09",
      "local_end_date": "2003-07-13",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        },
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        },
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - L'Argentière (FRA) 2003",
      "event_id": 316,
      "url": "/api/v1/events/316",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-07-24 00:00:00 UTC",
      "ends_at": "2003-07-25 00:00:00 UTC",
      "local_start_date": "2003-07-24",
      "local_end_date": "2003-07-25",
      "timezone": null,
      "d_cats": [
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Val Daone (ITA) 2003",
      "event_id": 317,
      "url": "/api/v1/events/317",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-07-25 00:00:00 UTC",
      "ends_at": "2003-07-26 00:00:00 UTC",
      "local_start_date": "2003-07-25",
      "local_end_date": "2003-07-26",
      "timezone": null,
      "d_cats": [
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Aviles (ESP) 2003",
      "event_id": 318,
      "url": "/api/v1/events/318",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-08-21 00:00:00 UTC",
      "ends_at": "2003-08-22 00:00:00 UTC",
      "local_start_date": "2003-08-21",
      "local_end_date": "2003-08-22",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Rovereto (ITA) 2003",
      "event_id": 319,
      "url": "/api/v1/events/319",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-09-11 00:00:00 UTC",
      "ends_at": "2003-09-12 00:00:00 UTC",
      "local_start_date": "2003-09-11",
      "local_end_date": "2003-09-12",
      "timezone": null,
      "d_cats": [
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Aprica (ITA) 2003",
      "event_id": 320,
      "url": "/api/v1/events/320",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-10-02 00:00:00 UTC",
      "ends_at": "2003-10-03 00:00:00 UTC",
      "local_start_date": "2003-10-02",
      "local_end_date": "2003-10-03",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Prague (CZE) 2003",
      "event_id": 321,
      "url": "/api/v1/events/321",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-10-10 00:00:00 UTC",
      "ends_at": "2003-10-11 00:00:00 UTC",
      "local_start_date": "2003-10-10",
      "local_end_date": "2003-10-11",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Valence (FRA) 2003",
      "event_id": 322,
      "url": "/api/v1/events/322",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-10-30 00:00:00 UTC",
      "ends_at": "2003-10-31 00:00:00 UTC",
      "local_start_date": "2003-10-30",
      "local_end_date": "2003-10-31",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Kranj (SLO) 2003",
      "event_id": 323,
      "url": "/api/v1/events/323",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-11-14 00:00:00 UTC",
      "ends_at": "2003-11-16 00:00:00 UTC",
      "local_start_date": "2003-11-14",
      "local_end_date": "2003-11-16",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Shenzen (CHN) 2003",
      "event_id": 324,
      "url": "/api/v1/events/324",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-11-21 00:00:00 UTC",
      "ends_at": "2003-11-23 00:00:00 UTC",
      "local_start_date": "2003-11-21",
      "local_end_date": "2003-11-23",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        },
        {
          "id": 2,
          "name": "SPEED Men",
          "category_rounds": []
        },
        {
          "id": 6,
          "name": "SPEED Women",
          "category_rounds": []
        }
      ]
    },
    {
      "event": "UIAA Worldcup - Edinburgh (GBR) 2003",
      "event_id": 325,
      "url": "/api/v1/events/325",
      "infosheet_url": null,
      "additional_info_url": null,
      "starts_at": "2003-12-04 00:00:00 UTC",
      "ends_at": "2003-12-07 00:00:00 UTC",
      "local_start_date": "2003-12-04",
      "local_end_date": "2003-12-07",
      "timezone": null,
      "d_cats": [
        {
          "id": 1,
          "name": "LEAD Men",
          "category_rounds": []
        },
        {
          "id": 5,
          "name": "LEAD Women",
          "category_rounds": []
        },
        {
          "id": 3,
          "name": "BOULDER Men",
          "category_rounds": []
        },
        {
          "id": 7,
          "name": "BOULDER Women",
          "category_rounds": []
        }
      ]
    }
  ]
}
```


</details>

### Event Information
```http request
GET https://components.ifsc-climbing.org/results-api.php?api=event_top3&event_id=1291
```
<details>
  <summary>Show Response</summary>

```json
{
  "id": 1291,
  "name": "IFSC World Cup Hachioji 2023",
  "league_id": 1,
  "league_season_id": 418,
  "season_id": 35,
  "starts_at": "2023-04-20 15:00:00 UTC",
  "ends_at": "2023-04-23 14:59:00 UTC",
  "local_start_date": "2023-04-21",
  "local_end_date": "2023-04-23",
  "timezone": {
    "value": "Asia/Tokyo"
  },
  "location": "Hachioji",
  "public_information": {
    "organiser_name": "Hachioji",
    "organiser_url": null,
    "venue_name": null,
    "description": null
  },
  "cup_name": "IFSC Climbing World Cup 2023",
  "country": "JPN",
  "registration_deadline": "2023-04-06T23:59:00.000Z",
  "athlete_self_registration": false,
  "team_official_self_registration": false,
  "event_logo": "https://d1n1qj9geboqnb.cloudfront.net/ifsc/public/0kuumvcxxnlvpusftfebx0w9zpjx",
  "series_logo": "https://d1n1qj9geboqnb.cloudfront.net/ifsc/public/6rxgrdo2es92kcviae3zaai6en64",
  "cover": null,
  "is_paraclimbing_event": false,
  "self_judged": false,
  "d_cats": [
    {
      "dcat_id": 3,
      "event_id": 1291,
      "dcat_name": "BOULDER Men",
      "discipline_kind": "boulder",
      "category_id": 5424,
      "category_name": "Men",
      "top_3_results": [
        {
          "athlete_id": 11675,
          "rank": 1,
          "name": "Schalck Mejdi",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "FRA",
          "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
          "federation_id": 10
        },
        {
          "athlete_id": 373,
          "rank": 2,
          "name": "Van Duysen Hannes",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "BEL",
          "flag_url": "https://ifsc.results.info/images/flags/BEL.png",
          "federation_id": 9
        },
        {
          "athlete_id": 547,
          "rank": 3,
          "name": "Jenft Paul",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "FRA",
          "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
          "federation_id": 10
        }
      ]
    },
    {
      "dcat_id": 7,
      "event_id": 1291,
      "dcat_name": "BOULDER Women",
      "discipline_kind": "boulder",
      "category_id": 5425,
      "category_name": "Women",
      "top_3_results": [
        {
          "athlete_id": 1811,
          "rank": 1,
          "name": "Raboutou Brooke",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "USA",
          "flag_url": "https://ifsc.results.info/images/flags/USA.png",
          "federation_id": 20
        },
        {
          "athlete_id": 3113,
          "rank": 2,
          "name": "Meul Hannah",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "GER",
          "flag_url": "https://ifsc.results.info/images/flags/GER.png",
          "federation_id": 6
        },
        {
          "athlete_id": 12297,
          "rank": 3,
          "name": "Matsufuji Anon",
          "paraclimbing_sport_class": null,
          "sport_class_status": null,
          "country": "JPN",
          "flag_url": "https://ifsc.results.info/images/flags/JPN.png",
          "federation_id": 25
        }
      ]
    }
  ],
  "disciplines": [
    {
      "id": 1536,
      "kind": "boulder",
      "settings": null
    }
  ],
  "computed_combined_categories": [],
  "team_ranking_disciplines": [
    "boulder"
  ],
  "team_ranking_url": "/api/v1/events/1291/team_results/"
}
```
</details>

```http request
GET https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=418
```
<details>
  <summary>Show Response</summary>

```json
{
    "events": [
        {
            "event": "IFSC World Cup Hachioji 2023",
            "event_id": 1291,
            "url": "/api/v1/events/1291",
            "infosheet_url": "https://ifsc.results.info/events/1291/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-04-20 15:00:00 UTC",
            "ends_at": "2023-04-23 14:59:00 UTC",
            "local_start_date": "2023-04-21",
            "local_end_date": "2023-04-23",
            "timezone": {
                "value": "Asia/Tokyo"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7669,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8120,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 9891,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9891/startlist"
                                },
                                {
                                    "id": 9892,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/9892/startlist"
                                },
                                {
                                    "id": 9893,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/9893/startlist"
                                },
                                {
                                    "id": 9894,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/9894/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 9899,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9899/startlist"
                                },
                                {
                                    "id": 9900,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/9900/startlist"
                                },
                                {
                                    "id": 9901,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/9901/startlist"
                                },
                                {
                                    "id": 9902,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/9902/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7670,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8121,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 9895,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9895/startlist"
                                },
                                {
                                    "id": 9896,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/9896/startlist"
                                },
                                {
                                    "id": 9897,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/9897/startlist"
                                },
                                {
                                    "id": 9898,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/9898/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 9903,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9903/startlist"
                                },
                                {
                                    "id": 9904,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/9904/startlist"
                                },
                                {
                                    "id": 9905,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/9905/startlist"
                                },
                                {
                                    "id": 9906,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/9906/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Seoul 2023",
            "event_id": 1292,
            "url": "/api/v1/events/1292",
            "infosheet_url": "https://ifsc.results.info/events/1292/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-04-27 15:00:00 UTC",
            "ends_at": "2023-04-30 14:59:00 UTC",
            "local_start_date": "2023-04-28",
            "local_end_date": "2023-04-30",
            "timezone": {
                "value": "Asia/Seoul"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7671,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8154,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10143,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10143/startlist"
                                },
                                {
                                    "id": 10144,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10144/startlist"
                                },
                                {
                                    "id": 10145,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10145/startlist"
                                },
                                {
                                    "id": 10146,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10146/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7672,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8155,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10147,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10147/startlist"
                                },
                                {
                                    "id": 10148,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10148/startlist"
                                },
                                {
                                    "id": 10149,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10149/startlist"
                                },
                                {
                                    "id": 10150,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10150/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7673,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8530,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8530/startlist"
                                },
                                {
                                    "id": 8531,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8531/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8084,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9775,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9775/startlist"
                                },
                                {
                                    "id": 9776,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9776/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7674,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8532,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8532/startlist"
                                },
                                {
                                    "id": 8533,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8533/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8085,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9777,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9777/startlist"
                                },
                                {
                                    "id": 9778,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9778/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Jakarta 2023",
            "event_id": 1293,
            "url": "/api/v1/events/1293",
            "infosheet_url": "https://ifsc.results.info/events/1293/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-05-05 17:00:00 UTC",
            "ends_at": "2023-05-07 16:59:00 UTC",
            "local_start_date": "2023-05-06",
            "local_end_date": "2023-05-07",
            "timezone": {
                "value": "Asia/Jakarta"
            },
            "d_cats": [
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7675,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8534,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8534/startlist"
                                },
                                {
                                    "id": 8535,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8535/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8088,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9783,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9783/startlist"
                                },
                                {
                                    "id": 9784,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9784/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7676,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8536,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8536/startlist"
                                },
                                {
                                    "id": 8537,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8537/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8089,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9785,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9785/startlist"
                                },
                                {
                                    "id": 9786,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9786/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Salt Lake City 2023",
            "event_id": 1294,
            "url": "/api/v1/events/1294",
            "infosheet_url": "https://ifsc.results.info/events/1294/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-05-19 06:00:00 UTC",
            "ends_at": "2023-05-22 05:59:00 UTC",
            "local_start_date": "2023-05-19",
            "local_end_date": "2023-05-21",
            "timezone": {
                "value": "America/Denver"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7677,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8194,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10325,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10325/startlist"
                                },
                                {
                                    "id": 10326,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10326/startlist"
                                },
                                {
                                    "id": 10327,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10327/startlist"
                                },
                                {
                                    "id": 10328,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10328/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8196,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10333,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10333/startlist"
                                },
                                {
                                    "id": 10334,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10334/startlist"
                                },
                                {
                                    "id": 10335,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10335/startlist"
                                },
                                {
                                    "id": 10336,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10336/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7678,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8195,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10329,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10329/startlist"
                                },
                                {
                                    "id": 10330,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10330/startlist"
                                },
                                {
                                    "id": 10331,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10331/startlist"
                                },
                                {
                                    "id": 10332,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10332/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8197,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10337,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10337/startlist"
                                },
                                {
                                    "id": 10338,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10338/startlist"
                                },
                                {
                                    "id": 10339,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10339/startlist"
                                },
                                {
                                    "id": 10340,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10340/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7679,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8558,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8558/startlist"
                                },
                                {
                                    "id": 8559,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8559/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8090,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9787,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9787/startlist"
                                },
                                {
                                    "id": 9788,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9788/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7680,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8560,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8560/startlist"
                                },
                                {
                                    "id": 8561,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8561/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8091,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9789,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9789/startlist"
                                },
                                {
                                    "id": 9790,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9790/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Prague 2023",
            "event_id": 1295,
            "url": "/api/v1/events/1295",
            "infosheet_url": "https://ifsc.results.info/events/1295/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-06-01 22:00:00 UTC",
            "ends_at": "2023-06-04 21:59:00 UTC",
            "local_start_date": "2023-06-02",
            "local_end_date": "2023-06-04",
            "timezone": {
                "value": "Europe/Prague"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7681,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8206,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10385,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10385/startlist"
                                },
                                {
                                    "id": 10386,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10386/startlist"
                                },
                                {
                                    "id": 10387,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10387/startlist"
                                },
                                {
                                    "id": 10388,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10388/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8208,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10393,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10393/startlist"
                                },
                                {
                                    "id": 10394,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10394/startlist"
                                },
                                {
                                    "id": 10395,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10395/startlist"
                                },
                                {
                                    "id": 10396,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10396/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7682,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8207,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10389,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10389/startlist"
                                },
                                {
                                    "id": 10390,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10390/startlist"
                                },
                                {
                                    "id": 10391,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10391/startlist"
                                },
                                {
                                    "id": 10392,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10392/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8209,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10397,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10397/startlist"
                                },
                                {
                                    "id": 10398,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10398/startlist"
                                },
                                {
                                    "id": 10399,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10399/startlist"
                                },
                                {
                                    "id": 10400,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10400/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Brixen 2023",
            "event_id": 1296,
            "url": "/api/v1/events/1296",
            "infosheet_url": "https://ifsc.results.info/events/1296/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-06-08 22:00:00 UTC",
            "ends_at": "2023-06-11 21:59:00 UTC",
            "local_start_date": "2023-06-09",
            "local_end_date": "2023-06-11",
            "timezone": {
                "value": "Europe/Rome"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7683,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8202,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10369,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10369/startlist"
                                },
                                {
                                    "id": 10370,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10370/startlist"
                                },
                                {
                                    "id": 10371,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10371/startlist"
                                },
                                {
                                    "id": 10372,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10372/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8204,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10377,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10377/startlist"
                                },
                                {
                                    "id": 10378,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10378/startlist"
                                },
                                {
                                    "id": 10379,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10379/startlist"
                                },
                                {
                                    "id": 10380,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10380/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7684,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8203,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10373,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10373/startlist"
                                },
                                {
                                    "id": 10374,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10374/startlist"
                                },
                                {
                                    "id": 10375,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10375/startlist"
                                },
                                {
                                    "id": 10376,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10376/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8205,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10381,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10381/startlist"
                                },
                                {
                                    "id": 10382,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10382/startlist"
                                },
                                {
                                    "id": 10383,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10383/startlist"
                                },
                                {
                                    "id": 10384,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10384/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Innsbruck 2023",
            "event_id": 1297,
            "url": "/api/v1/events/1297",
            "infosheet_url": "https://ifsc.results.info/events/1297/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-06-13 22:00:00 UTC",
            "ends_at": "2023-06-18 21:59:00 UTC",
            "local_start_date": "2023-06-14",
            "local_end_date": "2023-06-18",
            "timezone": {
                "value": "Europe/Vienna"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7685,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8250,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10565,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10565/startlist"
                                },
                                {
                                    "id": 10566,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10566/startlist"
                                },
                                {
                                    "id": 10567,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10567/startlist"
                                },
                                {
                                    "id": 10568,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10568/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8252,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10573,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10573/startlist"
                                },
                                {
                                    "id": 10574,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10574/startlist"
                                },
                                {
                                    "id": 10575,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10575/startlist"
                                },
                                {
                                    "id": 10576,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10576/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7686,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8251,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10569,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10569/startlist"
                                },
                                {
                                    "id": 10570,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10570/startlist"
                                },
                                {
                                    "id": 10571,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10571/startlist"
                                },
                                {
                                    "id": 10572,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10572/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8253,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10577,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10577/startlist"
                                },
                                {
                                    "id": 10578,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10578/startlist"
                                },
                                {
                                    "id": 10579,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10579/startlist"
                                },
                                {
                                    "id": 10580,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10580/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7687,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8622,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8622/startlist",
                                    "ranking": "/api/v1/routes/8622/results"
                                },
                                {
                                    "id": 8623,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8623/startlist",
                                    "ranking": "/api/v1/routes/8623/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8124,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 9907,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9907/startlist",
                                    "ranking": "/api/v1/routes/9907/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8264,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10591,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10591/startlist",
                                    "ranking": "/api/v1/routes/10591/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7688,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8624,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8624/startlist",
                                    "ranking": "/api/v1/routes/8624/results"
                                },
                                {
                                    "id": 8625,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8625/startlist",
                                    "ranking": "/api/v1/routes/8625/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8125,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 9908,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/9908/startlist",
                                    "ranking": "/api/v1/routes/9908/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8265,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10592,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10592/startlist",
                                    "ranking": "/api/v1/routes/10592/results"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Villars 2023",
            "event_id": 1298,
            "url": "/api/v1/events/1298",
            "infosheet_url": "https://ifsc.results.info/events/1298/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-06-29 22:00:00 UTC",
            "ends_at": "2023-07-02 21:59:00 UTC",
            "local_start_date": "2023-06-30",
            "local_end_date": "2023-07-02",
            "timezone": {
                "value": "Europe/Zurich"
            },
            "d_cats": [
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7689,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8626,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8626/startlist",
                                    "ranking": "/api/v1/routes/8626/results"
                                },
                                {
                                    "id": 8627,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8627/startlist",
                                    "ranking": "/api/v1/routes/8627/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8260,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10587,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10587/startlist",
                                    "ranking": "/api/v1/routes/10587/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8262,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10589,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10589/startlist",
                                    "ranking": "/api/v1/routes/10589/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7690,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8628,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8628/startlist",
                                    "ranking": "/api/v1/routes/8628/results"
                                },
                                {
                                    "id": 8629,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8629/startlist",
                                    "ranking": "/api/v1/routes/8629/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8261,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10588,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10588/startlist",
                                    "ranking": "/api/v1/routes/10588/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8263,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10590,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10590/startlist",
                                    "ranking": "/api/v1/routes/10590/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7691,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8630,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8630/startlist"
                                },
                                {
                                    "id": 8631,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8631/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8118,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9887,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9887/startlist"
                                },
                                {
                                    "id": 9888,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9888/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7692,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8632,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8632/startlist"
                                },
                                {
                                    "id": 8633,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8633/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8119,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9889,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9889/startlist"
                                },
                                {
                                    "id": 9890,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9890/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Chamonix 2023",
            "event_id": 1299,
            "url": "/api/v1/events/1299",
            "infosheet_url": "https://ifsc.results.info/events/1299/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-07-06 22:00:00 UTC",
            "ends_at": "2023-07-09 21:59:00 UTC",
            "local_start_date": "2023-07-07",
            "local_end_date": "2023-07-09",
            "timezone": {
                "value": "Europe/Paris"
            },
            "d_cats": [
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7693,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8634,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8634/startlist",
                                    "ranking": "/api/v1/routes/8634/results"
                                },
                                {
                                    "id": 8635,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8635/startlist",
                                    "ranking": "/api/v1/routes/8635/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8330,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10735,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10735/startlist",
                                    "ranking": "/api/v1/routes/10735/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8332,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10737,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10737/startlist",
                                    "ranking": "/api/v1/routes/10737/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7694,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8636,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8636/startlist",
                                    "ranking": "/api/v1/routes/8636/results"
                                },
                                {
                                    "id": 8637,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8637/startlist",
                                    "ranking": "/api/v1/routes/8637/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8331,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10736,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10736/startlist",
                                    "ranking": "/api/v1/routes/10736/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8333,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10738,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10738/startlist",
                                    "ranking": "/api/v1/routes/10738/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7695,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8638,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8638/startlist"
                                },
                                {
                                    "id": 8639,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8639/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8334,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 10739,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/10739/startlist"
                                },
                                {
                                    "id": 10740,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/10740/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7696,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8640,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8640/startlist"
                                },
                                {
                                    "id": 8641,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8641/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8335,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 10741,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/10741/startlist"
                                },
                                {
                                    "id": 10742,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/10742/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Briançon 2023",
            "event_id": 1300,
            "url": "/api/v1/events/1300",
            "infosheet_url": "https://ifsc.results.info/events/1300/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-07-13 22:00:00 UTC",
            "ends_at": "2023-07-15 21:59:00 UTC",
            "local_start_date": "2023-07-14",
            "local_end_date": "2023-07-15",
            "timezone": {
                "value": "Europe/Paris"
            },
            "d_cats": [
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7697,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8642,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8642/startlist",
                                    "ranking": "/api/v1/routes/8642/results"
                                },
                                {
                                    "id": 8643,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8643/startlist",
                                    "ranking": "/api/v1/routes/8643/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8316,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10707,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10707/startlist",
                                    "ranking": "/api/v1/routes/10707/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8328,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10733,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10733/startlist",
                                    "ranking": "/api/v1/routes/10733/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7698,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8644,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8644/startlist",
                                    "ranking": "/api/v1/routes/8644/results"
                                },
                                {
                                    "id": 8645,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8645/startlist",
                                    "ranking": "/api/v1/routes/8645/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8317,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10708,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10708/startlist",
                                    "ranking": "/api/v1/routes/10708/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8329,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10734,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10734/startlist",
                                    "ranking": "/api/v1/routes/10734/results"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Championships Bern 2023",
            "event_id": 1301,
            "url": "/api/v1/events/1301",
            "infosheet_url": "https://ifsc.results.info/events/1301/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-07-31 22:00:00 UTC",
            "ends_at": "2023-08-12 21:59:00 UTC",
            "local_start_date": "2023-08-01",
            "local_end_date": "2023-08-12",
            "timezone": {
                "value": "Europe/Zurich"
            },
            "d_cats": [
                {
                    "id": 3,
                    "name": "BOULDER Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7699,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8318,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10709,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10709/startlist"
                                },
                                {
                                    "id": 10710,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10710/startlist"
                                },
                                {
                                    "id": 10711,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10711/startlist"
                                },
                                {
                                    "id": 10712,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10712/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8320,
                            "kind": "boulder",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10717,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10717/startlist"
                                },
                                {
                                    "id": 10718,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10718/startlist"
                                },
                                {
                                    "id": 10719,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10719/startlist"
                                },
                                {
                                    "id": 10720,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10720/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 7,
                    "name": "BOULDER Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7700,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups",
                            "routes": []
                        },
                        {
                            "category_round_id": 8319,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 group",
                            "routes": [
                                {
                                    "id": 10713,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10713/startlist"
                                },
                                {
                                    "id": 10714,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10714/startlist"
                                },
                                {
                                    "id": 10715,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10715/startlist"
                                },
                                {
                                    "id": 10716,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10716/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8321,
                            "kind": "boulder",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": [
                                {
                                    "id": 10721,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10721/startlist"
                                },
                                {
                                    "id": 10722,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/10722/startlist"
                                },
                                {
                                    "id": 10723,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/10723/startlist"
                                },
                                {
                                    "id": 10724,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/10724/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7701,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 groups, 2 routes",
                            "routes": []
                        },
                        {
                            "category_round_id": 8322,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10725,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10725/startlist",
                                    "ranking": "/api/v1/routes/10725/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8324,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10727,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10727/startlist",
                                    "ranking": "/api/v1/routes/10727/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7702,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 groups, 2 routes",
                            "routes": []
                        },
                        {
                            "category_round_id": 8323,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10726,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10726/startlist",
                                    "ranking": "/api/v1/routes/10726/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8325,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 10728,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/10728/startlist",
                                    "ranking": "/api/v1/routes/10728/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7703,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8670,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8670/startlist"
                                },
                                {
                                    "id": 8671,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8671/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8326,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 10729,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/10729/startlist"
                                },
                                {
                                    "id": 10730,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/10730/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7704,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8672,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8672/startlist"
                                },
                                {
                                    "id": 8673,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8673/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8327,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 10731,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/10731/startlist"
                                },
                                {
                                    "id": 10732,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/10732/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 617,
                    "name": "BOULDER&LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 8348,
                            "kind": "boulder&lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": []
                        },
                        {
                            "category_round_id": 8350,
                            "kind": "boulder&lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": []
                        },
                        {
                            "category_round_id": 8352,
                            "kind": "boulder&lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": []
                        }
                    ]
                },
                {
                    "id": 618,
                    "name": "BOULDER&LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 8349,
                            "kind": "boulder&lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": []
                        },
                        {
                            "category_round_id": 8351,
                            "kind": "boulder&lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": []
                        },
                        {
                            "category_round_id": 8353,
                            "kind": "boulder&lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Finals",
                            "routes": []
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Koper 2023",
            "event_id": 1302,
            "url": "/api/v1/events/1302",
            "infosheet_url": "https://ifsc.results.info/events/1302/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-09-07 22:00:00 UTC",
            "ends_at": "2023-09-09 21:59:00 UTC",
            "local_start_date": "2023-09-08",
            "local_end_date": "2023-09-09",
            "timezone": {
                "value": "Europe/Ljubljana"
            },
            "d_cats": [
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7705,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8674,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8674/startlist",
                                    "ranking": "/api/v1/routes/8674/results"
                                },
                                {
                                    "id": 8675,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8675/startlist",
                                    "ranking": "/api/v1/routes/8675/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8432,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11073,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11073/startlist",
                                    "ranking": "/api/v1/routes/11073/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8434,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11075,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11075/startlist",
                                    "ranking": "/api/v1/routes/11075/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7706,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8676,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8676/startlist",
                                    "ranking": "/api/v1/routes/8676/results"
                                },
                                {
                                    "id": 8677,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8677/startlist",
                                    "ranking": "/api/v1/routes/8677/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8433,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11074,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11074/startlist",
                                    "ranking": "/api/v1/routes/11074/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8435,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11076,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11076/startlist",
                                    "ranking": "/api/v1/routes/11076/results"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "event": "IFSC World Cup Wujiang 2023",
            "event_id": 1303,
            "url": "/api/v1/events/1303",
            "infosheet_url": "https://ifsc.results.info/events/1303/infosheet",
            "additional_info_url": null,
            "starts_at": "2023-09-21 16:00:00 UTC",
            "ends_at": "2023-09-24 15:59:00 UTC",
            "local_start_date": "2023-09-22",
            "local_end_date": "2023-09-24",
            "timezone": {
                "value": "Asia/Chongqing"
            },
            "d_cats": [
                {
                    "id": 1,
                    "name": "LEAD Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7707,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8678,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8678/startlist",
                                    "ranking": "/api/v1/routes/8678/results"
                                },
                                {
                                    "id": 8679,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8679/startlist",
                                    "ranking": "/api/v1/routes/8679/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8500,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11269,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11269/startlist",
                                    "ranking": "/api/v1/routes/11269/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8502,
                            "kind": "lead",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11271,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11271/startlist",
                                    "ranking": "/api/v1/routes/11271/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 5,
                    "name": "LEAD Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7708,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 2 routes",
                            "routes": [
                                {
                                    "id": 8680,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8680/startlist",
                                    "ranking": "/api/v1/routes/8680/results"
                                },
                                {
                                    "id": 8681,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8681/startlist",
                                    "ranking": "/api/v1/routes/8681/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8501,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11270,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11270/startlist",
                                    "ranking": "/api/v1/routes/11270/results"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8503,
                            "kind": "lead",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: 1 route",
                            "routes": [
                                {
                                    "id": 11272,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/11272/startlist",
                                    "ranking": "/api/v1/routes/11272/results"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 2,
                    "name": "SPEED Men",
                    "category_rounds": [
                        {
                            "category_round_id": 7709,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8682,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8682/startlist"
                                },
                                {
                                    "id": 8683,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8683/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8086,
                            "kind": "speed",
                            "category": "Men",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9779,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9779/startlist"
                                },
                                {
                                    "id": 9780,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9780/startlist"
                                }
                            ]
                        }
                    ]
                },
                {
                    "id": 6,
                    "name": "SPEED Women",
                    "category_rounds": [
                        {
                            "category_round_id": 7710,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC: Qualification",
                            "routes": [
                                {
                                    "id": 8684,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/8684/startlist"
                                },
                                {
                                    "id": 8685,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/8685/startlist"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8087,
                            "kind": "speed",
                            "category": "Women",
                            "schedule": null,
                            "format": "IFSC 2023: Elimination",
                            "routes": [
                                {
                                    "id": 9781,
                                    "name": "A",
                                    "startlist": "/api/v1/routes/9781/startlist"
                                },
                                {
                                    "id": 9782,
                                    "name": "B",
                                    "startlist": "/api/v1/routes/9782/startlist"
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}
```
</details>

```http request
GET https://ifsc.results.info/api/v1/events/1291
```
<details>
  <summary>Show Response</summary>

```json
{
    "id": 1291,
    "name": "IFSC World Cup Hachioji 2023",
    "league_id": 1,
    "league_season_id": 418,
    "season_id": 35,
    "starts_at": "2023-04-20 15:00:00 UTC",
    "ends_at": "2023-04-23 14:59:00 UTC",
    "local_start_date": "2023-04-21",
    "local_end_date": "2023-04-23",
    "timezone": {
        "value": "Asia/Tokyo"
    },
    "location": "Hachioji",
    "registration_url": "/api/v1/events/1291/registrations",
    "public_information": {
        "organiser_name": "Hachioji",
        "organiser_url": null,
        "venue_name": null,
        "description": null
    },
    "cup_name": "IFSC Climbing World Cup 2023",
    "country": "JPN",
    "registration_deadline": "2023-04-06T23:59:00.000Z",
    "athlete_self_registration": false,
    "team_official_self_registration": false,
    "event_logo": "https://d1n1qj9geboqnb.cloudfront.net/ifsc/public/0kuumvcxxnlvpusftfebx0w9zpjx",
    "series_logo": "https://d1n1qj9geboqnb.cloudfront.net/ifsc/public/6rxgrdo2es92kcviae3zaai6en64",
    "cover": null,
    "infosheet_url": "https://ifsc.results.info/events/1291/infosheet",
    "additional_info_url": null,
    "is_paraclimbing_event": false,
    "self_judged": false,
    "d_cats": [
        {
            "dcat_id": 3,
            "event_id": 1291,
            "dcat_name": "BOULDER Men",
            "discipline_kind": "boulder",
            "category_id": 5424,
            "category_name": "Men",
            "status": "finished",
            "status_as_of": "2023-04-23 10:06:08 UTC",
            "ranking_as_of": "2023-04-23 09:48:31 UTC",
            "category_rounds": [
                {
                    "category_round_id": 7669,
                    "kind": "boulder",
                    "name": "Qualification",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-21 12:15:52 UTC",
                    "result_url": "/api/v1/category_rounds/7669/results",
                    "starting_groups": [
                        {
                            "id": 145,
                            "name": "Group A",
                            "ranking": "/api/v1/starting_groups/145/results",
                            "routes": [
                                {
                                    "id": 8490,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8490/startlist"
                                },
                                {
                                    "id": 8491,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8491/startlist"
                                },
                                {
                                    "id": 8492,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8492/startlist"
                                },
                                {
                                    "id": 8493,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8493/startlist"
                                },
                                {
                                    "id": 8494,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8494/startlist"
                                }
                            ]
                        },
                        {
                            "id": 146,
                            "name": "Group B",
                            "ranking": "/api/v1/starting_groups/146/results",
                            "routes": [
                                {
                                    "id": 8495,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8495/startlist"
                                },
                                {
                                    "id": 8496,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8496/startlist"
                                },
                                {
                                    "id": 8497,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8497/startlist"
                                },
                                {
                                    "id": 8498,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8498/startlist"
                                },
                                {
                                    "id": 8499,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8499/startlist"
                                }
                            ]
                        }
                    ],
                    "combined_stages": [],
                    "format": "IFSC: 2 groups",
                    "routes": []
                },
                {
                    "category_round_id": 8120,
                    "kind": "boulder",
                    "name": "Semi-final",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-23 04:17:38 UTC",
                    "result_url": "/api/v1/category_rounds/8120/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: 1 group",
                    "routes": [
                        {
                            "id": 9891,
                            "name": "1",
                            "startlist": "/api/v1/routes/9891/startlist"
                        },
                        {
                            "id": 9892,
                            "name": "2",
                            "startlist": "/api/v1/routes/9892/startlist"
                        },
                        {
                            "id": 9893,
                            "name": "3",
                            "startlist": "/api/v1/routes/9893/startlist"
                        },
                        {
                            "id": 9894,
                            "name": "4",
                            "startlist": "/api/v1/routes/9894/startlist"
                        }
                    ]
                },
                {
                    "category_round_id": 8122,
                    "kind": "boulder",
                    "name": "Final",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-23 10:03:03 UTC",
                    "result_url": "/api/v1/category_rounds/8122/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: Finals",
                    "routes": [
                        {
                            "id": 9899,
                            "name": "1",
                            "startlist": "/api/v1/routes/9899/startlist"
                        },
                        {
                            "id": 9900,
                            "name": "2",
                            "startlist": "/api/v1/routes/9900/startlist"
                        },
                        {
                            "id": 9901,
                            "name": "3",
                            "startlist": "/api/v1/routes/9901/startlist"
                        },
                        {
                            "id": 9902,
                            "name": "4",
                            "startlist": "/api/v1/routes/9902/startlist"
                        }
                    ]
                }
            ],
            "full_results_url": "/api/v1/events/1291/result/3",
            "top_3_results": [
                {
                    "athlete_id": 11675,
                    "rank": 1,
                    "name": "Schalck Mejdi",
                    "firstname": "Mejdi",
                    "lastname": "SCHALCK",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "4",
                    "country": "FRA",
                    "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
                    "federation_id": 10,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 17,
                            "score": "4T4z 6 6",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8490,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 07:48:02 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8491,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 07:57:54 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8492,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:08:19 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8493,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:22:24 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8494,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:29:47 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 4,
                            "score": "1T4z 2 9",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:20:33 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 7,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:31:17 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:41:51 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:50:29 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 1,
                            "score": "2T3z 7 7",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:24:39 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:50:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:19:31 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:48:30 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 373,
                    "rank": 2,
                    "name": "Van Duysen Hannes",
                    "firstname": "Hannes",
                    "lastname": "VAN DUYSEN",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "77",
                    "country": "BEL",
                    "flag_url": "https://ifsc.results.info/images/flags/BEL.png",
                    "federation_id": 9,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 11,
                            "score": "3T5z 9 15",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8495,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 09:50:59 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8496,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 7,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:02:11 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8497,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 16,
                                    "zone": true,
                                    "zone_tries": 9,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:12:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8498,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:19:21 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8499,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:30:38 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 5,
                            "score": "1T4z 8 14",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:51:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 8,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:02:01 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:13:16 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:23:45 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 2,
                            "score": "1T3z 2 11",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 9,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:20:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:45:51 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:14:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:44:09 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 547,
                    "rank": 3,
                    "name": "Jenft Paul",
                    "firstname": "Paul",
                    "lastname": "JENFT",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "7",
                    "country": "FRA",
                    "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
                    "federation_id": 10,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 7,
                            "score": "4T5z 10 9",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8490,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:02:56 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8491,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:14:58 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8492,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:23:44 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8493,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:37:54 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8494,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:46:58 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 1,
                            "score": "2T4z 11 13",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:07:50 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:18:40 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:28:46 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:38:57 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 3,
                            "score": "1T3z 3 3",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:37:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:02:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:33:52 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 10:02:00 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "dcat_id": 7,
            "event_id": 1291,
            "dcat_name": "BOULDER Women",
            "discipline_kind": "boulder",
            "category_id": 5425,
            "category_name": "Women",
            "status": "finished",
            "status_as_of": "2023-04-22 10:00:32 UTC",
            "ranking_as_of": "2023-04-22 09:44:50 UTC",
            "category_rounds": [
                {
                    "category_round_id": 7670,
                    "kind": "boulder",
                    "name": "Qualification",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-21 04:00:59 UTC",
                    "result_url": "/api/v1/category_rounds/7670/results",
                    "starting_groups": [
                        {
                            "id": 147,
                            "name": "Group A",
                            "ranking": "/api/v1/starting_groups/147/results",
                            "routes": [
                                {
                                    "id": 8500,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8500/startlist"
                                },
                                {
                                    "id": 8501,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8501/startlist"
                                },
                                {
                                    "id": 8502,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8502/startlist"
                                },
                                {
                                    "id": 8503,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8503/startlist"
                                },
                                {
                                    "id": 8504,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8504/startlist"
                                }
                            ]
                        },
                        {
                            "id": 148,
                            "name": "Group B",
                            "ranking": "/api/v1/starting_groups/148/results",
                            "routes": [
                                {
                                    "id": 8505,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8505/startlist"
                                },
                                {
                                    "id": 8506,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8506/startlist"
                                },
                                {
                                    "id": 8507,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8507/startlist"
                                },
                                {
                                    "id": 8508,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8508/startlist"
                                },
                                {
                                    "id": 8509,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8509/startlist"
                                }
                            ]
                        }
                    ],
                    "combined_stages": [],
                    "format": "IFSC: 2 groups",
                    "routes": []
                },
                {
                    "category_round_id": 8121,
                    "kind": "boulder",
                    "name": "Semi-final",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-22 04:22:33 UTC",
                    "result_url": "/api/v1/category_rounds/8121/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: 1 group",
                    "routes": [
                        {
                            "id": 9895,
                            "name": "1",
                            "startlist": "/api/v1/routes/9895/startlist"
                        },
                        {
                            "id": 9896,
                            "name": "2",
                            "startlist": "/api/v1/routes/9896/startlist"
                        },
                        {
                            "id": 9897,
                            "name": "3",
                            "startlist": "/api/v1/routes/9897/startlist"
                        },
                        {
                            "id": 9898,
                            "name": "4",
                            "startlist": "/api/v1/routes/9898/startlist"
                        }
                    ]
                },
                {
                    "category_round_id": 8123,
                    "kind": "boulder",
                    "name": "Final",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-22 09:55:10 UTC",
                    "result_url": "/api/v1/category_rounds/8123/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: Finals",
                    "routes": [
                        {
                            "id": 9903,
                            "name": "1",
                            "startlist": "/api/v1/routes/9903/startlist"
                        },
                        {
                            "id": 9904,
                            "name": "2",
                            "startlist": "/api/v1/routes/9904/startlist"
                        },
                        {
                            "id": 9905,
                            "name": "3",
                            "startlist": "/api/v1/routes/9905/startlist"
                        },
                        {
                            "id": 9906,
                            "name": "4",
                            "startlist": "/api/v1/routes/9906/startlist"
                        }
                    ]
                }
            ],
            "full_results_url": "/api/v1/events/1291/result/7",
            "top_3_results": [
                {
                    "athlete_id": 1811,
                    "rank": 1,
                    "name": "Raboutou Brooke",
                    "firstname": "Brooke",
                    "lastname": "RABOUTOU",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "142",
                    "country": "USA",
                    "flag_url": "https://ifsc.results.info/images/flags/USA.png",
                    "federation_id": 20,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 3,
                            "score": "5T5z 12 12",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8505,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:06:41 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8506,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:17:37 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8507,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 8,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:36:57 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8508,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:38:45 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8509,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:48:31 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 3,
                            "score": "2T4z 5 6",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:39:20 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:49:29 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:56:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:10:43 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 1,
                            "score": "3T4z 6 6",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:25:44 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:50:48 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:18:57 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:44:50 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 3113,
                    "rank": 2,
                    "name": "Meul Hannah",
                    "firstname": "Hannah",
                    "lastname": "MEUL",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "143",
                    "country": "GER",
                    "flag_url": "https://ifsc.results.info/images/flags/GER.png",
                    "federation_id": 6,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 7,
                            "score": "4T5z 6 5",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8505,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:12:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8506,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:23:16 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8507,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:36:55 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8508,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:44:08 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8509,
                                    "route_name": "5",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:57:47 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 1,
                            "score": "2T4z 4 5",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:18:25 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:28:42 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:36:00 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:49:43 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 2,
                            "score": "1T3z 1 8",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:34:01 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:59:31 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:27:52 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": false,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:54:08 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 12297,
                    "rank": 3,
                    "name": "Matsufuji Anon",
                    "firstname": "Anon",
                    "lastname": "MATSUFUJI",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "111",
                    "country": "JPN",
                    "flag_url": "https://ifsc.results.info/images/flags/JPN.png",
                    "federation_id": 25,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 1,
                            "score": "5T5z 5 5",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8500,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:54:40 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8501,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:05:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8502,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:15:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8503,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:25:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8504,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:36:34 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 4,
                            "score": "2T3z 5 6",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:44:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:52:15 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:05:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:15:52 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 3,
                            "score": "0T3z 0 7",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:24:05 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:46:13 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:16:21 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:42:23 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ],
    "dcats": [
        {
            "dcat_id": 3,
            "event_id": 1291,
            "dcat_name": "BOULDER Men",
            "discipline_kind": "boulder",
            "category_id": 5424,
            "category_name": "Men",
            "status": "finished",
            "status_as_of": "2023-04-23 10:06:08 UTC",
            "ranking_as_of": "2023-04-23 09:48:31 UTC",
            "category_rounds": [
                {
                    "category_round_id": 7669,
                    "kind": "boulder",
                    "name": "Qualification",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-21 12:15:52 UTC",
                    "result_url": "/api/v1/category_rounds/7669/results",
                    "starting_groups": [
                        {
                            "id": 145,
                            "name": "Group A",
                            "ranking": "/api/v1/starting_groups/145/results",
                            "routes": [
                                {
                                    "id": 8490,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8490/startlist"
                                },
                                {
                                    "id": 8491,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8491/startlist"
                                },
                                {
                                    "id": 8492,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8492/startlist"
                                },
                                {
                                    "id": 8493,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8493/startlist"
                                },
                                {
                                    "id": 8494,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8494/startlist"
                                }
                            ]
                        },
                        {
                            "id": 146,
                            "name": "Group B",
                            "ranking": "/api/v1/starting_groups/146/results",
                            "routes": [
                                {
                                    "id": 8495,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8495/startlist"
                                },
                                {
                                    "id": 8496,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8496/startlist"
                                },
                                {
                                    "id": 8497,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8497/startlist"
                                },
                                {
                                    "id": 8498,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8498/startlist"
                                },
                                {
                                    "id": 8499,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8499/startlist"
                                }
                            ]
                        }
                    ],
                    "combined_stages": [],
                    "format": "IFSC: 2 groups",
                    "routes": []
                },
                {
                    "category_round_id": 8120,
                    "kind": "boulder",
                    "name": "Semi-final",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-23 04:17:38 UTC",
                    "result_url": "/api/v1/category_rounds/8120/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: 1 group",
                    "routes": [
                        {
                            "id": 9891,
                            "name": "1",
                            "startlist": "/api/v1/routes/9891/startlist"
                        },
                        {
                            "id": 9892,
                            "name": "2",
                            "startlist": "/api/v1/routes/9892/startlist"
                        },
                        {
                            "id": 9893,
                            "name": "3",
                            "startlist": "/api/v1/routes/9893/startlist"
                        },
                        {
                            "id": 9894,
                            "name": "4",
                            "startlist": "/api/v1/routes/9894/startlist"
                        }
                    ]
                },
                {
                    "category_round_id": 8122,
                    "kind": "boulder",
                    "name": "Final",
                    "category": "Men",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-23 10:03:03 UTC",
                    "result_url": "/api/v1/category_rounds/8122/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: Finals",
                    "routes": [
                        {
                            "id": 9899,
                            "name": "1",
                            "startlist": "/api/v1/routes/9899/startlist"
                        },
                        {
                            "id": 9900,
                            "name": "2",
                            "startlist": "/api/v1/routes/9900/startlist"
                        },
                        {
                            "id": 9901,
                            "name": "3",
                            "startlist": "/api/v1/routes/9901/startlist"
                        },
                        {
                            "id": 9902,
                            "name": "4",
                            "startlist": "/api/v1/routes/9902/startlist"
                        }
                    ]
                }
            ],
            "full_results_url": "/api/v1/events/1291/result/3",
            "top_3_results": [
                {
                    "athlete_id": 11675,
                    "rank": 1,
                    "name": "Schalck Mejdi",
                    "firstname": "Mejdi",
                    "lastname": "SCHALCK",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "4",
                    "country": "FRA",
                    "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
                    "federation_id": 10,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 17,
                            "score": "4T4z 6 6",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8490,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 07:48:02 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8491,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 07:57:54 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8492,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:08:19 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8493,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:22:24 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8494,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:29:47 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 4,
                            "score": "1T4z 2 9",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:20:33 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 7,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:31:17 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:41:51 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:50:29 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 1,
                            "score": "2T3z 7 7",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:24:39 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:50:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:19:31 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:48:30 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 373,
                    "rank": 2,
                    "name": "Van Duysen Hannes",
                    "firstname": "Hannes",
                    "lastname": "VAN DUYSEN",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "77",
                    "country": "BEL",
                    "flag_url": "https://ifsc.results.info/images/flags/BEL.png",
                    "federation_id": 9,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 11,
                            "score": "3T5z 9 15",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8495,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 09:50:59 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8496,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 7,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:02:11 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8497,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 16,
                                    "zone": true,
                                    "zone_tries": 9,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:12:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8498,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:19:21 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8499,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 10:30:38 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 5,
                            "score": "1T4z 8 14",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 02:51:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 8,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:02:01 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:13:16 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:23:45 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 2,
                            "score": "1T3z 2 11",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 9,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:20:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:45:51 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:14:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:44:09 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 547,
                    "rank": 3,
                    "name": "Jenft Paul",
                    "firstname": "Paul",
                    "lastname": "JENFT",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "7",
                    "country": "FRA",
                    "flag_url": "https://ifsc.results.info/images/flags/FRA.png",
                    "federation_id": 10,
                    "rounds": [
                        {
                            "category_round_id": 7669,
                            "round_name": "Qualification",
                            "rank": 7,
                            "score": "4T5z 10 9",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8490,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:02:56 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8491,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:14:58 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8492,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:23:44 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8493,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:37:54 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8494,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 08:46:58 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8120,
                            "round_name": "Semi-final",
                            "rank": 1,
                            "score": "2T4z 11 13",
                            "ascents": [
                                {
                                    "route_id": 9891,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:07:50 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9892,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 9,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:18:40 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9893,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:28:46 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9894,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 03:38:57 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8122,
                            "round_name": "Final",
                            "rank": 3,
                            "score": "1T3z 3 3",
                            "ascents": [
                                {
                                    "route_id": 9899,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 08:37:53 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9900,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:02:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9901,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 09:33:52 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9902,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": false,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-23 10:02:00 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        {
            "dcat_id": 7,
            "event_id": 1291,
            "dcat_name": "BOULDER Women",
            "discipline_kind": "boulder",
            "category_id": 5425,
            "category_name": "Women",
            "status": "finished",
            "status_as_of": "2023-04-22 10:00:32 UTC",
            "ranking_as_of": "2023-04-22 09:44:50 UTC",
            "category_rounds": [
                {
                    "category_round_id": 7670,
                    "kind": "boulder",
                    "name": "Qualification",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-21 04:00:59 UTC",
                    "result_url": "/api/v1/category_rounds/7670/results",
                    "starting_groups": [
                        {
                            "id": 147,
                            "name": "Group A",
                            "ranking": "/api/v1/starting_groups/147/results",
                            "routes": [
                                {
                                    "id": 8500,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8500/startlist"
                                },
                                {
                                    "id": 8501,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8501/startlist"
                                },
                                {
                                    "id": 8502,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8502/startlist"
                                },
                                {
                                    "id": 8503,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8503/startlist"
                                },
                                {
                                    "id": 8504,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8504/startlist"
                                }
                            ]
                        },
                        {
                            "id": 148,
                            "name": "Group B",
                            "ranking": "/api/v1/starting_groups/148/results",
                            "routes": [
                                {
                                    "id": 8505,
                                    "name": "1",
                                    "startlist": "/api/v1/routes/8505/startlist"
                                },
                                {
                                    "id": 8506,
                                    "name": "2",
                                    "startlist": "/api/v1/routes/8506/startlist"
                                },
                                {
                                    "id": 8507,
                                    "name": "3",
                                    "startlist": "/api/v1/routes/8507/startlist"
                                },
                                {
                                    "id": 8508,
                                    "name": "4",
                                    "startlist": "/api/v1/routes/8508/startlist"
                                },
                                {
                                    "id": 8509,
                                    "name": "5",
                                    "startlist": "/api/v1/routes/8509/startlist"
                                }
                            ]
                        }
                    ],
                    "combined_stages": [],
                    "format": "IFSC: 2 groups",
                    "routes": []
                },
                {
                    "category_round_id": 8121,
                    "kind": "boulder",
                    "name": "Semi-final",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-22 04:22:33 UTC",
                    "result_url": "/api/v1/category_rounds/8121/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: 1 group",
                    "routes": [
                        {
                            "id": 9895,
                            "name": "1",
                            "startlist": "/api/v1/routes/9895/startlist"
                        },
                        {
                            "id": 9896,
                            "name": "2",
                            "startlist": "/api/v1/routes/9896/startlist"
                        },
                        {
                            "id": 9897,
                            "name": "3",
                            "startlist": "/api/v1/routes/9897/startlist"
                        },
                        {
                            "id": 9898,
                            "name": "4",
                            "startlist": "/api/v1/routes/9898/startlist"
                        }
                    ]
                },
                {
                    "category_round_id": 8123,
                    "kind": "boulder",
                    "name": "Final",
                    "category": "Women",
                    "schedule": null,
                    "status": "finished",
                    "status_as_of": "2023-04-22 09:55:10 UTC",
                    "result_url": "/api/v1/category_rounds/8123/results",
                    "starting_groups": [],
                    "combined_stages": [],
                    "format": "IFSC: Finals",
                    "routes": [
                        {
                            "id": 9903,
                            "name": "1",
                            "startlist": "/api/v1/routes/9903/startlist"
                        },
                        {
                            "id": 9904,
                            "name": "2",
                            "startlist": "/api/v1/routes/9904/startlist"
                        },
                        {
                            "id": 9905,
                            "name": "3",
                            "startlist": "/api/v1/routes/9905/startlist"
                        },
                        {
                            "id": 9906,
                            "name": "4",
                            "startlist": "/api/v1/routes/9906/startlist"
                        }
                    ]
                }
            ],
            "full_results_url": "/api/v1/events/1291/result/7",
            "top_3_results": [
                {
                    "athlete_id": 1811,
                    "rank": 1,
                    "name": "Raboutou Brooke",
                    "firstname": "Brooke",
                    "lastname": "RABOUTOU",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "142",
                    "country": "USA",
                    "flag_url": "https://ifsc.results.info/images/flags/USA.png",
                    "federation_id": 20,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 3,
                            "score": "5T5z 12 12",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8505,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:06:41 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8506,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:17:37 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8507,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 8,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:36:57 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8508,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:38:45 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8509,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:48:31 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 3,
                            "score": "2T4z 5 6",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:39:20 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:49:29 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:56:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:10:43 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 1,
                            "score": "3T4z 6 6",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:25:44 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:50:48 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:18:57 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:44:50 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 3113,
                    "rank": 2,
                    "name": "Meul Hannah",
                    "firstname": "Hannah",
                    "lastname": "MEUL",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "143",
                    "country": "GER",
                    "flag_url": "https://ifsc.results.info/images/flags/GER.png",
                    "federation_id": 6,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 7,
                            "score": "4T5z 6 5",
                            "starting_group": "Group B",
                            "ascents": [
                                {
                                    "route_id": 8505,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:12:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8506,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:23:16 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8507,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:36:55 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8508,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:44:08 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8509,
                                    "route_name": "5",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:57:47 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 1,
                            "score": "2T4z 4 5",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:18:25 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:28:42 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:36:00 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:49:43 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 2,
                            "score": "1T3z 1 8",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:34:01 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:59:31 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 8,
                                    "zone": true,
                                    "zone_tries": 5,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:27:52 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": false,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:54:08 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                },
                {
                    "athlete_id": 12297,
                    "rank": 3,
                    "name": "Matsufuji Anon",
                    "firstname": "Anon",
                    "lastname": "MATSUFUJI",
                    "paraclimbing_sport_class": null,
                    "sport_class_status": null,
                    "bib": "111",
                    "country": "JPN",
                    "flag_url": "https://ifsc.results.info/images/flags/JPN.png",
                    "federation_id": 25,
                    "rounds": [
                        {
                            "category_round_id": 7670,
                            "round_name": "Qualification",
                            "rank": 1,
                            "score": "5T5z 5 5",
                            "starting_group": "Group A",
                            "ascents": [
                                {
                                    "route_id": 8500,
                                    "route_name": "1",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 00:54:40 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8501,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:05:06 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8502,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:15:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8503,
                                    "route_name": "4",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:25:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 8504,
                                    "route_name": "5",
                                    "top": true,
                                    "top_tries": 1,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-21 01:36:34 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8121,
                            "round_name": "Semi-final",
                            "rank": 4,
                            "score": "2T3z 5 6",
                            "ascents": [
                                {
                                    "route_id": 9895,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:44:38 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9896,
                                    "route_name": "2",
                                    "top": true,
                                    "top_tries": 2,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 03:52:15 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9897,
                                    "route_name": "3",
                                    "top": true,
                                    "top_tries": 3,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:05:12 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9898,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 3,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 04:15:52 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        },
                        {
                            "category_round_id": 8123,
                            "round_name": "Final",
                            "rank": 3,
                            "score": "0T3z 0 7",
                            "ascents": [
                                {
                                    "route_id": 9903,
                                    "route_name": "1",
                                    "top": false,
                                    "top_tries": 5,
                                    "zone": true,
                                    "zone_tries": 1,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:24:05 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9904,
                                    "route_name": "2",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": true,
                                    "zone_tries": 4,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 08:46:13 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9905,
                                    "route_name": "3",
                                    "top": false,
                                    "top_tries": 4,
                                    "zone": true,
                                    "zone_tries": 2,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:16:21 +00:00",
                                    "status": "confirmed"
                                },
                                {
                                    "route_id": 9906,
                                    "route_name": "4",
                                    "top": false,
                                    "top_tries": 6,
                                    "zone": false,
                                    "zone_tries": 6,
                                    "low_zone": false,
                                    "points": null,
                                    "low_zone_tries": null,
                                    "modified": "2023-04-22 09:42:23 +00:00",
                                    "status": "confirmed"
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ],
    "disciplines": [
        {
            "id": 1536,
            "kind": "boulder",
            "settings": null
        }
    ],
    "computed_combined_categories": [],
    "team_ranking_disciplines": [
        "boulder"
    ],
    "team_ranking_url": "/api/v1/events/1291/team_results/"
}
```
</details>


### Seasons
```http request
GET https://components.ifsc-climbing.org/results-api.php?api=index
```

<details>
  <summary>Show Response</summary>

```json
{
  "current": {
    "id": 36,
    "season": "2024",
    "url": "/api/v1/seasons/36",
    "discipline_kinds": [
      [
        0,
        "lead"
      ],
      [
        1,
        "speed"
      ],
      [
        2,
        "boulder"
      ],
      [
        3,
        "combined"
      ],
      [
        4,
        "boulder&lead"
      ]
    ],
    "leagues": [
      {
        "id": 431,
        "name": "World Cups and World Championships",
        "cups": [
          {
            "id": 94,
            "name": "IFSC Climbing World Cup 2024"
          }
        ],
        "url": "/api/v1/season_leagues/431"
      },
      {
        "id": 432,
        "name": "IFSC Youth",
        "cups": [],
        "url": "/api/v1/season_leagues/432"
      },
      {
        "id": 433,
        "name": "IFSC Paraclimbing",
        "cups": [],
        "url": "/api/v1/season_leagues/433"
      },
      {
        "id": 434,
        "name": "IFSC Asia Adults",
        "cups": [],
        "url": "/api/v1/season_leagues/434"
      },
      {
        "id": 435,
        "name": "IFSC Asia Youth",
        "cups": [],
        "url": "/api/v1/season_leagues/435"
      },
      {
        "id": 436,
        "name": "IFSC Europe Adults",
        "cups": [
          {
            "id": 97,
            "name": "IFSC-Europe Climbing European Cup 2024"
          }
        ],
        "url": "/api/v1/season_leagues/436"
      },
      {
        "id": 437,
        "name": "IFSC Europe Youth",
        "cups": [
          {
            "id": 95,
            "name": "IFSC-Europe Climbing European Youth Cup 2024"
          }
        ],
        "url": "/api/v1/season_leagues/437"
      },
      {
        "id": 438,
        "name": "Games",
        "cups": [],
        "url": "/api/v1/season_leagues/438"
      },
      {
        "id": 439,
        "name": "Other events",
        "cups": [],
        "url": "/api/v1/season_leagues/439"
      },
      {
        "id": 440,
        "name": "Masters and Promotional Events",
        "cups": [],
        "url": "/api/v1/season_leagues/440"
      },
      {
        "id": 441,
        "name": "IFSC Pan America Adults",
        "cups": [],
        "url": "/api/v1/season_leagues/441"
      }
    ]
  },
  "seasons": [
    {
      "id": 36,
      "name": "2024",
      "url": "/api/v1/seasons/36",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 431,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 94,
              "name": "IFSC Climbing World Cup 2024"
            }
          ],
          "url": "/api/v1/season_leagues/431"
        },
        {
          "id": 432,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/432"
        },
        {
          "id": 433,
          "name": "IFSC Paraclimbing",
          "cups": [],
          "url": "/api/v1/season_leagues/433"
        },
        {
          "id": 434,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/434"
        },
        {
          "id": 435,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/435"
        },
        {
          "id": 436,
          "name": "IFSC Europe Adults",
          "cups": [
            {
              "id": 97,
              "name": "IFSC-Europe Climbing European Cup 2024"
            }
          ],
          "url": "/api/v1/season_leagues/436"
        },
        {
          "id": 437,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 95,
              "name": "IFSC-Europe Climbing European Youth Cup 2024"
            }
          ],
          "url": "/api/v1/season_leagues/437"
        },
        {
          "id": 438,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/438"
        },
        {
          "id": 439,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/439"
        },
        {
          "id": 440,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/440"
        },
        {
          "id": 441,
          "name": "IFSC Pan America Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/441"
        }
      ]
    },
    {
      "id": 35,
      "name": "2023",
      "url": "/api/v1/seasons/35",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 418,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 90,
              "name": "IFSC Climbing World Cup 2023"
            }
          ],
          "url": "/api/v1/season_leagues/418"
        },
        {
          "id": 419,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/419"
        },
        {
          "id": 420,
          "name": "IFSC Paraclimbing",
          "cups": [],
          "url": "/api/v1/season_leagues/420"
        },
        {
          "id": 421,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/421"
        },
        {
          "id": 422,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/422"
        },
        {
          "id": 423,
          "name": "IFSC Europe Adults",
          "cups": [
            {
              "id": 92,
              "name": "IFSC-Europe Climbing European Cup 2023"
            }
          ],
          "url": "/api/v1/season_leagues/423"
        },
        {
          "id": 424,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 91,
              "name": "IFSC-Europe Climbing European Youth Cup 2023"
            }
          ],
          "url": "/api/v1/season_leagues/424"
        },
        {
          "id": 425,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/425"
        },
        {
          "id": 426,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/426"
        },
        {
          "id": 427,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/427"
        },
        {
          "id": 428,
          "name": "IFSC Pan America Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/428"
        },
        {
          "id": 429,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/429"
        },
        {
          "id": 430,
          "name": "IFSC Africa",
          "cups": [],
          "url": "/api/v1/season_leagues/430"
        }
      ]
    },
    {
      "id": 34,
      "name": "2022",
      "url": "/api/v1/seasons/34",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 404,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 72,
              "name": "IFSC Climbing World Cup 2022"
            }
          ],
          "url": "/api/v1/season_leagues/404"
        },
        {
          "id": 405,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/405"
        },
        {
          "id": 407,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/407"
        },
        {
          "id": 408,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/408"
        },
        {
          "id": 409,
          "name": "IFSC Europe Adults",
          "cups": [
            {
              "id": 78,
              "name": "IFSC-Europe Climbing European Cup 2022 - Lead"
            },
            {
              "id": 88,
              "name": "IFSC-Europe Climbing European Cup 2022 - Boulder"
            },
            {
              "id": 89,
              "name": "IFSC-Europe Climbing European Cup 2022 - Speed"
            }
          ],
          "url": "/api/v1/season_leagues/409"
        },
        {
          "id": 410,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 85,
              "name": "IFSC-Europe Climbing European Youth Cup 2022 - Lead"
            },
            {
              "id": 86,
              "name": "IFSC-Europe Climbing European Youth Cup 2022 - Boulder"
            },
            {
              "id": 87,
              "name": "IFSC-Europe Climbing European Youth Cup 2022 - Speed"
            }
          ],
          "url": "/api/v1/season_leagues/410"
        },
        {
          "id": 411,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/411"
        },
        {
          "id": 412,
          "name": "IFSC Pan America Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/412"
        },
        {
          "id": 413,
          "name": "IFSC Africa",
          "cups": [],
          "url": "/api/v1/season_leagues/413"
        },
        {
          "id": 414,
          "name": "IFSC Paraclimbing",
          "cups": [],
          "url": "/api/v1/season_leagues/414"
        },
        {
          "id": 415,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/415"
        },
        {
          "id": 416,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/416"
        },
        {
          "id": 417,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/417"
        }
      ]
    },
    {
      "id": 33,
      "name": "2021",
      "url": "/api/v1/seasons/33",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 388,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 66,
              "name": "IFSC Climbing World Cup 2021"
            }
          ],
          "url": "/api/v1/season_leagues/388"
        },
        {
          "id": 389,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/389"
        },
        {
          "id": 391,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/391"
        },
        {
          "id": 392,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/392"
        },
        {
          "id": 393,
          "name": "IFSC Europe Adults",
          "cups": [
            {
              "id": 68,
              "name": "IFSC Climbing European Cup 2021"
            }
          ],
          "url": "/api/v1/season_leagues/393"
        },
        {
          "id": 395,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/395"
        },
        {
          "id": 396,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/396"
        },
        {
          "id": 397,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/397"
        },
        {
          "id": 398,
          "name": "IFSC Pan America Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/398"
        },
        {
          "id": 399,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/399"
        },
        {
          "id": 400,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/400"
        },
        {
          "id": 401,
          "name": "IFSC Africa",
          "cups": [],
          "url": "/api/v1/season_leagues/401"
        },
        {
          "id": 402,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 73,
              "name": "IFSC Climbing European Youth Cup 2021 - Lead"
            },
            {
              "id": 74,
              "name": "IFSC Climbing European Youth Cup 2021 - Boulder"
            },
            {
              "id": 75,
              "name": "IFSC Climbing European Youth Cup 2021 - Speed"
            }
          ],
          "url": "/api/v1/season_leagues/402"
        },
        {
          "id": 403,
          "name": "IFSC Paraclimbing",
          "cups": [
            {
              "id": 71,
              "name": "IFSC Paraclimbing Cup"
            }
          ],
          "url": "/api/v1/season_leagues/403"
        }
      ]
    },
    {
      "id": 2,
      "name": "2020",
      "url": "/api/v1/seasons/2",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 14,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 3,
              "name": "IFSC Climbing World Cup 2020"
            }
          ],
          "url": "/api/v1/season_leagues/14"
        },
        {
          "id": 15,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/15"
        },
        {
          "id": 16,
          "name": "IFSC Paraclimbing (L)",
          "cups": [
            {
              "id": 7,
              "name": "IFSC Paraclimbing World Cup 2020"
            }
          ],
          "url": "/api/v1/season_leagues/16"
        },
        {
          "id": 17,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/17"
        },
        {
          "id": 18,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/18"
        },
        {
          "id": 19,
          "name": "IFSC Europe Adults",
          "cups": [
            {
              "id": 5,
              "name": "IFSC Climbing European Cup 2020"
            }
          ],
          "url": "/api/v1/season_leagues/19"
        },
        {
          "id": 20,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 8,
              "name": "IFSC Climbing European Youth Cup 2020"
            }
          ],
          "url": "/api/v1/season_leagues/20"
        },
        {
          "id": 24,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/24"
        },
        {
          "id": 25,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/25"
        },
        {
          "id": 26,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/26"
        },
        {
          "id": 27,
          "name": "IFSC Pan America Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/27"
        }
      ]
    },
    {
      "id": 32,
      "name": "2019",
      "url": "/api/v1/seasons/32",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 376,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 63,
              "name": "IFSC Climbing World Cup 2019"
            }
          ],
          "url": "/api/v1/season_leagues/376"
        },
        {
          "id": 377,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/377"
        },
        {
          "id": 378,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/378"
        },
        {
          "id": 379,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/379"
        },
        {
          "id": 380,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/380"
        },
        {
          "id": 381,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/381"
        },
        {
          "id": 382,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 64,
              "name": "European Youth Cup 2019"
            }
          ],
          "url": "/api/v1/season_leagues/382"
        },
        {
          "id": 383,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/383"
        },
        {
          "id": 384,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/384"
        },
        {
          "id": 385,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/385"
        },
        {
          "id": 386,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/386"
        },
        {
          "id": 387,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/387"
        }
      ]
    },
    {
      "id": 31,
      "name": "2018",
      "url": "/api/v1/seasons/31",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 364,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 61,
              "name": "IFSC Climbing Worldcup 2018"
            }
          ],
          "url": "/api/v1/season_leagues/364"
        },
        {
          "id": 365,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/365"
        },
        {
          "id": 366,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/366"
        },
        {
          "id": 367,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/367"
        },
        {
          "id": 368,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/368"
        },
        {
          "id": 369,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/369"
        },
        {
          "id": 370,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 62,
              "name": "European Youth Cup 2018"
            }
          ],
          "url": "/api/v1/season_leagues/370"
        },
        {
          "id": 371,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/371"
        },
        {
          "id": 372,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/372"
        },
        {
          "id": 373,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/373"
        },
        {
          "id": 374,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/374"
        },
        {
          "id": 375,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/375"
        }
      ]
    },
    {
      "id": 30,
      "name": "2017",
      "url": "/api/v1/seasons/30",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 352,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 59,
              "name": "IFSC Climbing Worldcup 2017"
            }
          ],
          "url": "/api/v1/season_leagues/352"
        },
        {
          "id": 353,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/353"
        },
        {
          "id": 354,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/354"
        },
        {
          "id": 355,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/355"
        },
        {
          "id": 356,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/356"
        },
        {
          "id": 357,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/357"
        },
        {
          "id": 358,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 60,
              "name": "European Youth Cup 2017"
            }
          ],
          "url": "/api/v1/season_leagues/358"
        },
        {
          "id": 359,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/359"
        },
        {
          "id": 360,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/360"
        },
        {
          "id": 361,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/361"
        },
        {
          "id": 362,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/362"
        },
        {
          "id": 363,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/363"
        }
      ]
    },
    {
      "id": 29,
      "name": "2016",
      "url": "/api/v1/seasons/29",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 340,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 57,
              "name": "IFSC Climbing Worldcup 2016"
            }
          ],
          "url": "/api/v1/season_leagues/340"
        },
        {
          "id": 341,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/341"
        },
        {
          "id": 342,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/342"
        },
        {
          "id": 343,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/343"
        },
        {
          "id": 344,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/344"
        },
        {
          "id": 345,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/345"
        },
        {
          "id": 346,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 58,
              "name": "European Youth Cup 2016"
            }
          ],
          "url": "/api/v1/season_leagues/346"
        },
        {
          "id": 347,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/347"
        },
        {
          "id": 348,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/348"
        },
        {
          "id": 349,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/349"
        },
        {
          "id": 350,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/350"
        },
        {
          "id": 351,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/351"
        }
      ]
    },
    {
      "id": 28,
      "name": "2015",
      "url": "/api/v1/seasons/28",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 328,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 55,
              "name": "IFSC Climbing Worldcup 2015"
            }
          ],
          "url": "/api/v1/season_leagues/328"
        },
        {
          "id": 329,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/329"
        },
        {
          "id": 330,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/330"
        },
        {
          "id": 331,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/331"
        },
        {
          "id": 332,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/332"
        },
        {
          "id": 333,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/333"
        },
        {
          "id": 334,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 56,
              "name": "European Youth Cup 2015"
            }
          ],
          "url": "/api/v1/season_leagues/334"
        },
        {
          "id": 335,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/335"
        },
        {
          "id": 336,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/336"
        },
        {
          "id": 337,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/337"
        },
        {
          "id": 338,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/338"
        },
        {
          "id": 339,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/339"
        }
      ]
    },
    {
      "id": 27,
      "name": "2014",
      "url": "/api/v1/seasons/27",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 316,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 53,
              "name": "IFSC Climbing Worldcup 2014"
            }
          ],
          "url": "/api/v1/season_leagues/316"
        },
        {
          "id": 317,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/317"
        },
        {
          "id": 318,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/318"
        },
        {
          "id": 319,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/319"
        },
        {
          "id": 320,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/320"
        },
        {
          "id": 321,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/321"
        },
        {
          "id": 322,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 54,
              "name": "European Youth Cup 2014"
            }
          ],
          "url": "/api/v1/season_leagues/322"
        },
        {
          "id": 323,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/323"
        },
        {
          "id": 324,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/324"
        },
        {
          "id": 325,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/325"
        },
        {
          "id": 326,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/326"
        },
        {
          "id": 327,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/327"
        }
      ]
    },
    {
      "id": 26,
      "name": "2013",
      "url": "/api/v1/seasons/26",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 304,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 51,
              "name": "IFSC Climbing Worldcup 2013"
            }
          ],
          "url": "/api/v1/season_leagues/304"
        },
        {
          "id": 305,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/305"
        },
        {
          "id": 306,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/306"
        },
        {
          "id": 307,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/307"
        },
        {
          "id": 308,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/308"
        },
        {
          "id": 309,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/309"
        },
        {
          "id": 310,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 52,
              "name": "European Youth Cup 2013"
            }
          ],
          "url": "/api/v1/season_leagues/310"
        },
        {
          "id": 311,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/311"
        },
        {
          "id": 312,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/312"
        },
        {
          "id": 313,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/313"
        },
        {
          "id": 314,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/314"
        },
        {
          "id": 315,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/315"
        }
      ]
    },
    {
      "id": 25,
      "name": "2012",
      "url": "/api/v1/seasons/25",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 292,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 48,
              "name": "IFSC Climbing Worldcup 2012"
            }
          ],
          "url": "/api/v1/season_leagues/292"
        },
        {
          "id": 293,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/293"
        },
        {
          "id": 294,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/294"
        },
        {
          "id": 295,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/295"
        },
        {
          "id": 296,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/296"
        },
        {
          "id": 297,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/297"
        },
        {
          "id": 298,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 49,
              "name": "European Youth Cup 2012"
            }
          ],
          "url": "/api/v1/season_leagues/298"
        },
        {
          "id": 299,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/299"
        },
        {
          "id": 300,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/300"
        },
        {
          "id": 301,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/301"
        },
        {
          "id": 302,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/302"
        },
        {
          "id": 303,
          "name": "Masters and Promotional Events",
          "cups": [
            {
              "id": 50,
              "name": "Rheintal Cup 2012"
            }
          ],
          "url": "/api/v1/season_leagues/303"
        }
      ]
    },
    {
      "id": 24,
      "name": "2011",
      "url": "/api/v1/seasons/24",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 280,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 45,
              "name": "IFSC Climbing Worldcup 2011"
            }
          ],
          "url": "/api/v1/season_leagues/280"
        },
        {
          "id": 281,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/281"
        },
        {
          "id": 282,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/282"
        },
        {
          "id": 283,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/283"
        },
        {
          "id": 284,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/284"
        },
        {
          "id": 285,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/285"
        },
        {
          "id": 286,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 46,
              "name": "European Youth Cup 2011"
            }
          ],
          "url": "/api/v1/season_leagues/286"
        },
        {
          "id": 287,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/287"
        },
        {
          "id": 288,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/288"
        },
        {
          "id": 289,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/289"
        },
        {
          "id": 290,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/290"
        },
        {
          "id": 291,
          "name": "Masters and Promotional Events",
          "cups": [
            {
              "id": 47,
              "name": "Rheintal Cup 2011"
            }
          ],
          "url": "/api/v1/season_leagues/291"
        }
      ]
    },
    {
      "id": 23,
      "name": "2010",
      "url": "/api/v1/seasons/23",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 268,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 43,
              "name": "IFSC Climbing Worldcup 2010"
            }
          ],
          "url": "/api/v1/season_leagues/268"
        },
        {
          "id": 269,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/269"
        },
        {
          "id": 270,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/270"
        },
        {
          "id": 271,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/271"
        },
        {
          "id": 272,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/272"
        },
        {
          "id": 273,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/273"
        },
        {
          "id": 274,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 44,
              "name": "10_EYC: European Youth Series 2010"
            }
          ],
          "url": "/api/v1/season_leagues/274"
        },
        {
          "id": 275,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/275"
        },
        {
          "id": 276,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/276"
        },
        {
          "id": 277,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/277"
        },
        {
          "id": 278,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/278"
        },
        {
          "id": 279,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/279"
        }
      ]
    },
    {
      "id": 22,
      "name": "2009",
      "url": "/api/v1/seasons/22",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 256,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 41,
              "name": "IFSC Climbing Worldcup 2009"
            }
          ],
          "url": "/api/v1/season_leagues/256"
        },
        {
          "id": 257,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/257"
        },
        {
          "id": 258,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/258"
        },
        {
          "id": 259,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/259"
        },
        {
          "id": 260,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/260"
        },
        {
          "id": 261,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/261"
        },
        {
          "id": 262,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 42,
              "name": "European Youth Series 2009"
            }
          ],
          "url": "/api/v1/season_leagues/262"
        },
        {
          "id": 263,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/263"
        },
        {
          "id": 264,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/264"
        },
        {
          "id": 265,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/265"
        },
        {
          "id": 266,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/266"
        },
        {
          "id": 267,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/267"
        }
      ]
    },
    {
      "id": 21,
      "name": "2008",
      "url": "/api/v1/seasons/21",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 244,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 39,
              "name": "IFSC Climbing Worldcup 2008"
            }
          ],
          "url": "/api/v1/season_leagues/244"
        },
        {
          "id": 245,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/245"
        },
        {
          "id": 246,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/246"
        },
        {
          "id": 247,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/247"
        },
        {
          "id": 248,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/248"
        },
        {
          "id": 249,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/249"
        },
        {
          "id": 250,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 40,
              "name": "European Youth Series 2008"
            }
          ],
          "url": "/api/v1/season_leagues/250"
        },
        {
          "id": 251,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/251"
        },
        {
          "id": 252,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/252"
        },
        {
          "id": 253,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/253"
        },
        {
          "id": 254,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/254"
        },
        {
          "id": 255,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/255"
        }
      ]
    },
    {
      "id": 20,
      "name": "2007",
      "url": "/api/v1/seasons/20",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 232,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 37,
              "name": "IFSC Climbing Worldcup 2007"
            }
          ],
          "url": "/api/v1/season_leagues/232"
        },
        {
          "id": 233,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/233"
        },
        {
          "id": 234,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/234"
        },
        {
          "id": 235,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/235"
        },
        {
          "id": 236,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/236"
        },
        {
          "id": 237,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/237"
        },
        {
          "id": 238,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 38,
              "name": "European Youth Series 2007"
            }
          ],
          "url": "/api/v1/season_leagues/238"
        },
        {
          "id": 239,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/239"
        },
        {
          "id": 240,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/240"
        },
        {
          "id": 241,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/241"
        },
        {
          "id": 242,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/242"
        },
        {
          "id": 243,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/243"
        }
      ]
    },
    {
      "id": 19,
      "name": "2006",
      "url": "/api/v1/seasons/19",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 220,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 35,
              "name": "UIAA Climbing Worldcup 2006"
            }
          ],
          "url": "/api/v1/season_leagues/220"
        },
        {
          "id": 221,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/221"
        },
        {
          "id": 222,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/222"
        },
        {
          "id": 223,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/223"
        },
        {
          "id": 224,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/224"
        },
        {
          "id": 225,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/225"
        },
        {
          "id": 226,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 36,
              "name": "European Youth Cup 2006"
            }
          ],
          "url": "/api/v1/season_leagues/226"
        },
        {
          "id": 227,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/227"
        },
        {
          "id": 228,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/228"
        },
        {
          "id": 229,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/229"
        },
        {
          "id": 230,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/230"
        },
        {
          "id": 231,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/231"
        }
      ]
    },
    {
      "id": 18,
      "name": "2005",
      "url": "/api/v1/seasons/18",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 208,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 33,
              "name": "UIAA Climbing Worldcup 2005"
            }
          ],
          "url": "/api/v1/season_leagues/208"
        },
        {
          "id": 209,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/209"
        },
        {
          "id": 210,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/210"
        },
        {
          "id": 211,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/211"
        },
        {
          "id": 212,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/212"
        },
        {
          "id": 213,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/213"
        },
        {
          "id": 214,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 34,
              "name": "European Youth Cup 2005"
            }
          ],
          "url": "/api/v1/season_leagues/214"
        },
        {
          "id": 215,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/215"
        },
        {
          "id": 216,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/216"
        },
        {
          "id": 217,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/217"
        },
        {
          "id": 218,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/218"
        },
        {
          "id": 219,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/219"
        }
      ]
    },
    {
      "id": 17,
      "name": "2004",
      "url": "/api/v1/seasons/17",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 196,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 31,
              "name": "UIAA Climbing-Worldcup 2004"
            }
          ],
          "url": "/api/v1/season_leagues/196"
        },
        {
          "id": 197,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/197"
        },
        {
          "id": 198,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/198"
        },
        {
          "id": 199,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/199"
        },
        {
          "id": 200,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/200"
        },
        {
          "id": 201,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/201"
        },
        {
          "id": 202,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 32,
              "name": "European Youth Cup 2004"
            }
          ],
          "url": "/api/v1/season_leagues/202"
        },
        {
          "id": 203,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/203"
        },
        {
          "id": 204,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/204"
        },
        {
          "id": 205,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/205"
        },
        {
          "id": 206,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/206"
        },
        {
          "id": 207,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/207"
        }
      ]
    },
    {
      "id": 16,
      "name": "2003",
      "url": "/api/v1/seasons/16",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 184,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 29,
              "name": "UIAA Climbing-Worldcup 2003"
            }
          ],
          "url": "/api/v1/season_leagues/184"
        },
        {
          "id": 185,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/185"
        },
        {
          "id": 186,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/186"
        },
        {
          "id": 187,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/187"
        },
        {
          "id": 188,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/188"
        },
        {
          "id": 189,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/189"
        },
        {
          "id": 190,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 30,
              "name": "European Youth Cup 2003"
            }
          ],
          "url": "/api/v1/season_leagues/190"
        },
        {
          "id": 191,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/191"
        },
        {
          "id": 192,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/192"
        },
        {
          "id": 193,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/193"
        },
        {
          "id": 194,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/194"
        },
        {
          "id": 195,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/195"
        }
      ]
    },
    {
      "id": 15,
      "name": "2002",
      "url": "/api/v1/seasons/15",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 172,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 27,
              "name": "UIAA Climbing-Worldcup 2002"
            }
          ],
          "url": "/api/v1/season_leagues/172"
        },
        {
          "id": 173,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/173"
        },
        {
          "id": 174,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/174"
        },
        {
          "id": 175,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/175"
        },
        {
          "id": 176,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/176"
        },
        {
          "id": 177,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/177"
        },
        {
          "id": 178,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 28,
              "name": "European Youth Cup 2002"
            }
          ],
          "url": "/api/v1/season_leagues/178"
        },
        {
          "id": 179,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/179"
        },
        {
          "id": 180,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/180"
        },
        {
          "id": 181,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/181"
        },
        {
          "id": 182,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/182"
        },
        {
          "id": 183,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/183"
        }
      ]
    },
    {
      "id": 14,
      "name": "2001",
      "url": "/api/v1/seasons/14",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 160,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 25,
              "name": "UIAA Climbing-Worldcup 2001"
            }
          ],
          "url": "/api/v1/season_leagues/160"
        },
        {
          "id": 161,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/161"
        },
        {
          "id": 162,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/162"
        },
        {
          "id": 163,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/163"
        },
        {
          "id": 164,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/164"
        },
        {
          "id": 165,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/165"
        },
        {
          "id": 166,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 26,
              "name": "European Youth Cup 2001"
            }
          ],
          "url": "/api/v1/season_leagues/166"
        },
        {
          "id": 167,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/167"
        },
        {
          "id": 168,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/168"
        },
        {
          "id": 169,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/169"
        },
        {
          "id": 170,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/170"
        },
        {
          "id": 171,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/171"
        }
      ]
    },
    {
      "id": 13,
      "name": "2000",
      "url": "/api/v1/seasons/13",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 148,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 23,
              "name": "UIAA Climbing-Worldcup 2000"
            }
          ],
          "url": "/api/v1/season_leagues/148"
        },
        {
          "id": 149,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/149"
        },
        {
          "id": 150,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/150"
        },
        {
          "id": 151,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/151"
        },
        {
          "id": 152,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/152"
        },
        {
          "id": 153,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/153"
        },
        {
          "id": 154,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 24,
              "name": "European Youth Cup 2000"
            }
          ],
          "url": "/api/v1/season_leagues/154"
        },
        {
          "id": 155,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/155"
        },
        {
          "id": 156,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/156"
        },
        {
          "id": 157,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/157"
        },
        {
          "id": 158,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/158"
        },
        {
          "id": 159,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/159"
        }
      ]
    },
    {
      "id": 12,
      "name": "1999",
      "url": "/api/v1/seasons/12",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 136,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 21,
              "name": "UIAA Climbing-Worldcup 1999"
            }
          ],
          "url": "/api/v1/season_leagues/136"
        },
        {
          "id": 137,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/137"
        },
        {
          "id": 138,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/138"
        },
        {
          "id": 139,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/139"
        },
        {
          "id": 140,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/140"
        },
        {
          "id": 141,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/141"
        },
        {
          "id": 142,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 22,
              "name": "European Youth Cup 1999"
            }
          ],
          "url": "/api/v1/season_leagues/142"
        },
        {
          "id": 143,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/143"
        },
        {
          "id": 144,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/144"
        },
        {
          "id": 145,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/145"
        },
        {
          "id": 146,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/146"
        },
        {
          "id": 147,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/147"
        }
      ]
    },
    {
      "id": 11,
      "name": "1998",
      "url": "/api/v1/seasons/11",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 124,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 18,
              "name": "UIAA Climbing-Worldcup 1998"
            }
          ],
          "url": "/api/v1/season_leagues/124"
        },
        {
          "id": 125,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/125"
        },
        {
          "id": 126,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/126"
        },
        {
          "id": 127,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/127"
        },
        {
          "id": 128,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/128"
        },
        {
          "id": 129,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/129"
        },
        {
          "id": 130,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 19,
              "name": "European Youth Cup 1998"
            }
          ],
          "url": "/api/v1/season_leagues/130"
        },
        {
          "id": 131,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/131"
        },
        {
          "id": 132,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/132"
        },
        {
          "id": 133,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/133"
        },
        {
          "id": 134,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/134"
        },
        {
          "id": 135,
          "name": "Masters and Promotional Events",
          "cups": [
            {
              "id": 20,
              "name": "Top Rock Challenge 1998 - Bouldering"
            }
          ],
          "url": "/api/v1/season_leagues/135"
        }
      ]
    },
    {
      "id": 10,
      "name": "1997",
      "url": "/api/v1/seasons/10",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 112,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 16,
              "name": "UIAA Climbing-Worldcup 1997"
            }
          ],
          "url": "/api/v1/season_leagues/112"
        },
        {
          "id": 113,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/113"
        },
        {
          "id": 114,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/114"
        },
        {
          "id": 115,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/115"
        },
        {
          "id": 116,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/116"
        },
        {
          "id": 117,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/117"
        },
        {
          "id": 118,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 17,
              "name": "European Youth Cup 1997"
            }
          ],
          "url": "/api/v1/season_leagues/118"
        },
        {
          "id": 119,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/119"
        },
        {
          "id": 120,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/120"
        },
        {
          "id": 121,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/121"
        },
        {
          "id": 122,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/122"
        },
        {
          "id": 123,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/123"
        }
      ]
    },
    {
      "id": 9,
      "name": "1996",
      "url": "/api/v1/seasons/9",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 100,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 14,
              "name": "UIAA Climbing-Worldcup 1996"
            }
          ],
          "url": "/api/v1/season_leagues/100"
        },
        {
          "id": 101,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/101"
        },
        {
          "id": 102,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/102"
        },
        {
          "id": 103,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/103"
        },
        {
          "id": 104,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/104"
        },
        {
          "id": 105,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/105"
        },
        {
          "id": 106,
          "name": "IFSC Europe Youth",
          "cups": [
            {
              "id": 15,
              "name": "European Youth Cup 1996"
            }
          ],
          "url": "/api/v1/season_leagues/106"
        },
        {
          "id": 107,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/107"
        },
        {
          "id": 108,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/108"
        },
        {
          "id": 109,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/109"
        },
        {
          "id": 110,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/110"
        },
        {
          "id": 111,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/111"
        }
      ]
    },
    {
      "id": 8,
      "name": "1995",
      "url": "/api/v1/seasons/8",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 88,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 13,
              "name": "UIAA Climbing-Worldcup 1995"
            }
          ],
          "url": "/api/v1/season_leagues/88"
        },
        {
          "id": 89,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/89"
        },
        {
          "id": 90,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/90"
        },
        {
          "id": 91,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/91"
        },
        {
          "id": 92,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/92"
        },
        {
          "id": 93,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/93"
        },
        {
          "id": 94,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/94"
        },
        {
          "id": 95,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/95"
        },
        {
          "id": 96,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/96"
        },
        {
          "id": 97,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/97"
        },
        {
          "id": 98,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/98"
        },
        {
          "id": 99,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/99"
        }
      ]
    },
    {
      "id": 7,
      "name": "1994",
      "url": "/api/v1/seasons/7",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 76,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 12,
              "name": "UIAA Climbing-Worldcup 1994"
            }
          ],
          "url": "/api/v1/season_leagues/76"
        },
        {
          "id": 77,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/77"
        },
        {
          "id": 78,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/78"
        },
        {
          "id": 79,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/79"
        },
        {
          "id": 80,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/80"
        },
        {
          "id": 81,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/81"
        },
        {
          "id": 82,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/82"
        },
        {
          "id": 83,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/83"
        },
        {
          "id": 84,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/84"
        },
        {
          "id": 85,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/85"
        },
        {
          "id": 86,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/86"
        },
        {
          "id": 87,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/87"
        }
      ]
    },
    {
      "id": 6,
      "name": "1993",
      "url": "/api/v1/seasons/6",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 64,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 11,
              "name": "UIAA Climbing-Worldcup 1993"
            }
          ],
          "url": "/api/v1/season_leagues/64"
        },
        {
          "id": 65,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/65"
        },
        {
          "id": 66,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/66"
        },
        {
          "id": 67,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/67"
        },
        {
          "id": 68,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/68"
        },
        {
          "id": 69,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/69"
        },
        {
          "id": 70,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/70"
        },
        {
          "id": 71,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/71"
        },
        {
          "id": 72,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/72"
        },
        {
          "id": 73,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/73"
        },
        {
          "id": 74,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/74"
        },
        {
          "id": 75,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/75"
        }
      ]
    },
    {
      "id": 5,
      "name": "1992",
      "url": "/api/v1/seasons/5",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 52,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 10,
              "name": "UIAA Climbing-Worldcup 1992"
            }
          ],
          "url": "/api/v1/season_leagues/52"
        },
        {
          "id": 53,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/53"
        },
        {
          "id": 54,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/54"
        },
        {
          "id": 55,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/55"
        },
        {
          "id": 56,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/56"
        },
        {
          "id": 57,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/57"
        },
        {
          "id": 58,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/58"
        },
        {
          "id": 59,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/59"
        },
        {
          "id": 60,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/60"
        },
        {
          "id": 61,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/61"
        },
        {
          "id": 62,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/62"
        },
        {
          "id": 63,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/63"
        }
      ]
    },
    {
      "id": 4,
      "name": "1991",
      "url": "/api/v1/seasons/4",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 40,
          "name": "World Cups and World Championships",
          "cups": [
            {
              "id": 9,
              "name": "UIAA Climbing-Worldcup 1991"
            }
          ],
          "url": "/api/v1/season_leagues/40"
        },
        {
          "id": 41,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/41"
        },
        {
          "id": 42,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/42"
        },
        {
          "id": 43,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/43"
        },
        {
          "id": 44,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/44"
        },
        {
          "id": 45,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/45"
        },
        {
          "id": 46,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/46"
        },
        {
          "id": 47,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/47"
        },
        {
          "id": 48,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/48"
        },
        {
          "id": 49,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/49"
        },
        {
          "id": 50,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/50"
        },
        {
          "id": 51,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/51"
        }
      ]
    },
    {
      "id": 3,
      "name": "1990",
      "url": "/api/v1/seasons/3",
      "discipline_kinds": [
        [
          0,
          "lead"
        ],
        [
          1,
          "speed"
        ],
        [
          2,
          "boulder"
        ],
        [
          3,
          "combined"
        ],
        [
          4,
          "boulder&lead"
        ]
      ],
      "leagues": [
        {
          "id": 28,
          "name": "World Cups and World Championships",
          "cups": [],
          "url": "/api/v1/season_leagues/28"
        },
        {
          "id": 29,
          "name": "IFSC Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/29"
        },
        {
          "id": 30,
          "name": "IFSC Paraclimbing (L)",
          "cups": [],
          "url": "/api/v1/season_leagues/30"
        },
        {
          "id": 31,
          "name": "IFSC Asia Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/31"
        },
        {
          "id": 32,
          "name": "IFSC Asia Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/32"
        },
        {
          "id": 33,
          "name": "IFSC Europe Adults",
          "cups": [],
          "url": "/api/v1/season_leagues/33"
        },
        {
          "id": 34,
          "name": "IFSC Europe Youth",
          "cups": [],
          "url": "/api/v1/season_leagues/34"
        },
        {
          "id": 35,
          "name": "IFSC Panam",
          "cups": [],
          "url": "/api/v1/season_leagues/35"
        },
        {
          "id": 36,
          "name": "IFSC Oceania",
          "cups": [],
          "url": "/api/v1/season_leagues/36"
        },
        {
          "id": 37,
          "name": "Games",
          "cups": [],
          "url": "/api/v1/season_leagues/37"
        },
        {
          "id": 38,
          "name": "Other events",
          "cups": [],
          "url": "/api/v1/season_leagues/38"
        },
        {
          "id": 39,
          "name": "Masters and Promotional Events",
          "cups": [],
          "url": "/api/v1/season_leagues/39"
        }
      ]
    }
  ]
}
```
</details>

### Live
```http request
GET https://ifsc.results.info/api/v1/live
```

<details>
    <summary>Show Response</summary>

```json
{
    "live": []
}
 ```
</details>