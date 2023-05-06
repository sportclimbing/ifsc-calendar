# IFSC Climbing Calendar and Streams
![downloads](https://img.shields.io/github/downloads/sportclimbing/ifsc-calendar/total?color=green&label=Downloads)
![update ralendar](https://github.com/sportclimbing/ifsc-calendar/actions/workflows/update-calendar.yml/badge.svg)

![ifsc-logo](resources/images/ifsc-logo.png)

### 📖 TL;DR
This automatically generates an up-to-date calendar you can subscribe to, to never miss an IFSC climbing event ever again.

#### How
Copy and paste this calendar URL (**https://calendar.ifsc.stream**) into your calendar subscriptions, and it will
automatically sync with your device. This works on iPhone, Google Calendar, Proton Calendar, etc... This should keep
you updated on future seasons as well.

Take a look at the **[setup guides](https://github.com/sportclimbing/ifsc-calendar/wiki)** for help.

Additionally, the calendar data is exported as `JSON` and can be viewed on this automatically updated
website:

## 👉 [https://ifsc.stream](https://ifsc.stream/)

### 👀 Intro
If you're constantly missing IFSC events because of a lacking calendar, or timezone confusions,
then you're at the right place.

This command line tool uses IFSC's API, plus some scraping (because some endpoints require 
authentication) to generate an up-to-date calendar with all necessary info.

### 🛠 Usage

#### Docker
Build Docker image
```shell
$ docker build --tag ifsc-calendar .
```
Generate `.ics` calendar file
```shell
$ docker run -it --volume "$PWD:/calendar" ifsc-calendar \
    --season 2023 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.ics"
```

Generate `.json` calendar file
```shell
$ docker run -it --volume "$PWD:/calendar" ifsc-calendar \
    --season 2023 \
    --league "World Cups and World Championships" \
    --output "/calendar/ifsc-calendar.json" \
    --format json
```

#### Build it yourself
Build executable
```shell
$ make
```

Generate `.ics` calendar file
```
$ ./build/ifsc-calendar.phar \
  --season 2023 \
  --league "World Cups and World Championships" \
  --output "ifsc-calendar.ics"
```

#### YouTube
If the optional flag `--fetch-youtube-urls` is passed, it'll attempt to find missing stream URLs from YouTube's
API. This requires an environment variable with the name `YOUTUBE_API_KEY` to be set, containing your API key.

To generate an API key, enable the API in your [Google Cloud Console](https://console.cloud.google.com/apis/api/youtube.googleapis.com/)
and [create credentials](https://console.cloud.google.com/apis/credentials).

```shell
$ export YOUTUBE_API_KEY=xxxxxxxxxxxx
```

Generate `.ics` calendar file
```shell
$ ./build/ifsc-calendar.phar \
  --season 2023 \
  --league "World Cups and World Championships" \
  --output "ifsc-calendar.ics" \
  --fetch-youtube-urls
```

### 🔧 Todo
 - [ ] Calculate average event duration and add it to `events.json` and to the calendar
 - [ ] Validate newly generated calendar before publishing site
 - [ ] Finish writing calendar setup guides
 - [ ] Cleanup PHP code
 - [ ] Add more tests
 - [ ] Make scraping more robust and fail on errors or missing data
 - [ ] Add Google Analytics (and cookie notice)
 - [ ] Check if there's an API to fetch events from instead of relying on scraping
 - [x] Disable youtube-fetch by default
 - [x] Add links to specific events to calendar
 - [x] Add `latest` tag to latest release
 - [x] Always serve asset from latest release on calendar URL
 - [x] Fetch stream links from YouTube API if none can be scraped
 - [x] Automatically regenerate calendar and update release

### IFSC API Endpoints
 - https://ifsc.results.info/api/v1/events/1291 (auth required)
 - https://components.ifsc-climbing.org/results-api.php?api=event_top3&event_id=1291
 - https://components.ifsc-climbing.org/results-api.php?api=season_leagues_calendar&league=418
 - https://components.ifsc-climbing.org/results-api.php?api=index

### Requirements
- PHP 8.2
- ext-dom
- ext-libxml

### Legal note
This is in no way affiliated with, or endorsed by IFSC.
