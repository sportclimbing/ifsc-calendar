dayjs.extend(window.dayjs_plugin_relativeTime);
dayjs.extend(window.dayjs_plugin_isBetween);

function sort_by_date(event1, event2) {
    let eventDate1 = new Date(event1.start_time);
    let eventDate2 = new Date(event2.start_time);

    if (eventDate1 < eventDate2) {
        return -1;
    }

    if (eventDate1 > eventDate2) {
        return 1;
    }

    return 0;
}

function event_is_streaming(event) {
    const now = dayjs();
    const eventStart = dayjs(event.start_time);

    return eventStart.isBetween(now, now.subtract(3, 'hour'));
}

function pretty_starts_in(event) {
    return dayjs(event.start_time).fromNow();
}

function pretty_started_ago(event) {
    return `Started ${dayjs(event.start_time).fromNow()}`;
}

function pretty_finished_ago(event) {
    return `Streamed ${dayjs(event.start_time).fromNow()}`;
}

function get_upcoming_events(jsonData) {
    const now = new Date();
    const upcomingEvents = jsonData.events.filter((event) => new Date(event.start_time) >= now);

    upcomingEvents.sort(sort_by_date);

    return upcomingEvents;
}

function sort_leagues_by_id(jsonData) {
    let leagues = [];

    jsonData.events.forEach((event) => {
        if (typeof leagues[event.id] === 'undefined') {
            leagues[event.id] = [];
        }

        leagues[event.id].push(event);
    });

    return leagues;
}

function element_is_in_viewport (el) {
    const rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function remove_hash() {
    let scrollV, scrollH, loc = window.location;
    if ("pushState" in history)
        history.pushState("", document.title, loc.pathname + loc.search);
    else {
        // Prevent scrolling by storing the page's current scroll offset
        scrollV = document.body.scrollTop;
        scrollH = document.body.scrollLeft;

        loc.hash = "";

        // Restore the scroll offset, should be flicker free
        document.body.scrollTop = scrollV;
        document.body.scrollLeft = scrollH;
    }
}

let hasScrolled = false;

const refresh = (async () => {
    const response = await fetch("events/events.json");
    const jsonData = await response.json();
    const upcomingEvents = get_upcoming_events(jsonData);
    const leagues = sort_leagues_by_id(jsonData);
    const now = new Date();
    const leagueTemplate = document.getElementById('ifsc-league');
    const accordion = document.getElementById('accordion');

    while (accordion.lastElementChild) {
        accordion.removeChild(accordion.lastElementChild);
    }

    leagues.forEach((league) => {
        const clone = leagueTemplate.content.cloneNode(true);

        clone.getElementById('ifsc-league-name').innerHTML = '🥇 ' + league[0].description.replace(/^IFSC - Climbing/, '');
        clone.getElementById('ifsc-league-name').setAttribute('data-target', `#event-${league[0].id}`);

        clone.getElementById('heading_id').id = `heading_${league[0].id}`;

        clone.getElementById('event-n').setAttribute('aria-labelledby', `event-${league[0].id}`);
        clone.getElementById('event-n').id = `event-${league[0].id}`;

        accordion.appendChild(clone);
    });

    let nextEvent = upcomingEvents.at(0);
    let selectedLeague = parseInt((window.location.hash || '').substring(7));

    if (!selectedLeague && nextEvent) {
        selectedLeague = nextEvent.id;
    }

    let leagueElement = document.getElementById(`event-${selectedLeague}`);
    leagueElement.classList.add('show');

    const template = document.getElementById("ifsc-event");
    let liveEvent = null;

    let lastEventFinished = false;

    jsonData.events.forEach((event) => {
        try {
            const clone = template.content.cloneNode(true);

            if (event.poster) {
                clone.getElementById('ifsc-poster').src = event.poster;
            } else {
                clone.getElementById('ifsc-poster').src = 'img/posters/230329_Poster_SEOUL23_thumb.jpg';
            }

            clone.getElementById('ifsc-name').innerText = `🏆 ${event.name}`;
            clone.getElementById('ifsc-description').innerText = '📆 ' + dayjs(event.start_time).format('MMMM D, YYYY [at] hh:mm A');

            if (event.stream_url) {
                clone.getElementById('button-stream').href = event.stream_url;
            } else {
                clone.getElementById('button-stream').href = 'https://www.youtube.com/@sportclimbing/streams';
            }

            let status = clone.getElementById('ifsc-status');

            if (event_is_streaming(event)) {
                clone.getElementById('ifsc-starts-in').innerText = `🔴 Live Now`;
                clone.getRootNode().firstChild.nextSibling.style.backgroundColor = '#f7f7f7';
                status.innerHTML = `🔴`;
                status.classList.add('text-danger');
                liveEvent = event;

                clone.getRootNode().firstChild.nextSibling.style.opacity = '100%'
                clone.getElementById('button-results').href = `https://ifsc.results.info/#/event/${event.id}`;
                document.getElementById(`event-${event.id}`).getElementsByTagName('ul')[0].appendChild(clone);
            } else if (new Date(event.start_time) > now) {
                clone.getElementById('ifsc-starts-in').innerText = `⏰ Starts ${pretty_starts_in(event)}`;

                if (!liveEvent && lastEventFinished) {
                    lastEventFinished = false;
                    status.innerHTML = `🟢`;
                    status.classList.add('text-success');

                    clone.getRootNode().firstChild.nextSibling.style.backgroundColor = 'rgba(246,245,245,0.4)';
                    clone.getRootNode().firstChild.nextSibling.style.opacity = '100%'
                } else {
                    clone.getRootNode().firstChild.nextSibling.style.opacity = '70%'
                    status.innerHTML = `⌛️`;
                    status.classList.add('text-warning');
                }

                clone.getElementById('button-results').style.display = 'none';
                document.getElementById(`event-${event.id}`).getElementsByTagName('ul')[0].appendChild(clone);
            } else {
                clone.getElementById('ifsc-starts-in').innerText = `⏰ ${pretty_finished_ago(event)}`;
                status.classList.add('text-danger');
                status.innerHTML = `🏁`;

                clone.getRootNode().firstChild.nextSibling.style.opacity = '70%'
                clone.getElementById('button-results').href = `https://ifsc.results.info/#/event/${event.id}`;

                lastEventFinished = true;
                document.getElementById(`event-${event.id}`).getElementsByTagName('ul')[0].appendChild(clone);
            }
        } catch (e) {
            console.log(e)
        }
    });

    if (!hasScrolled && !element_is_in_viewport(leagueElement)) {
        leagueElement.scrollIntoView();
        hasScrolled = true;
    }

    remove_hash();
});

(async () => {
    await refresh();
    window.setInterval(refresh, 1000 * 60);
})();
