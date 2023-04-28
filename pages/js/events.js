dayjs.extend(window.dayjs_plugin_relativeTime);
dayjs.extend(window.dayjs_plugin_isBetween);

function sort_by_date(event1, event2) {
    if (new Date(event1.start_time) < new Date(event2.start_time)) {
        return -1;
    }
    if (new Date(event1.start_time) > new Date(event2.start_time)) {
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
    return `Starts ${dayjs(event.start_time).fromNow()}`;
}

function pretty_started_ago(event) {
    return `Started ${dayjs(event.start_time).fromNow()}`;
}

function pretty_finished_ago(event) {
    return `Streamed ${dayjs(event.start_time).fromNow()}`;
}

const refresh = (async () => {
    const response = await fetch("events/events.json");
    const jsonData = await response.json();

    let pastEvents = [];
    let upcomingEvents = [];

    jsonData.events.forEach((event) => {
        let date = new Date(event.start_time)
        // var d = date.toLocaleString('es-ES', { timeZone: 'Europe/Madrid' });

        if (new Date() > date) {
            pastEvents.push(event);
        } else {
            upcomingEvents.push(event);
        }
    });

    upcomingEvents.sort(sort_by_date);
    pastEvents.sort(sort_by_date);

    let nextEvent = upcomingEvents.at(0);
    let nextLeague = [...pastEvents, ...upcomingEvents].filter((event) => {
        return event.description === nextEvent.description;
    });

    const container = document.getElementById("upcoming-events");
    const template = document.getElementById("ifsc-event");
    let now = new Date();
    let liveEvent = null;

    while (container.lastElementChild) {
        container.removeChild(container.lastElementChild);
    }

    nextLeague.forEach((event) => {
        try {
            const clone = template.content.cloneNode(true);

            clone.getElementById('ifsc-poster').src = event.poster;
            clone.getElementById('ifsc-description').innerText = event.description;
            clone.getElementById('ifsc-name').innerText = `👉 ${event.name}`;

            if (event.stream_url) {
                clone.getElementById('button-stream').href = event.stream_url;
            } else {
                clone.getElementById('button-stream').href = 'https://www.youtube.com/@sportclimbing/streams';
            }

            clone.getElementById('button-event').href = event.event_url;

            let status = clone.getElementById('ifsc-status');

            if (event_is_streaming(event)) {
                clone.getElementById('ifsc-starts-in').innerText = `⏰ ${pretty_started_ago(event)}`;
                status.innerHTML = `🔴 &nbsp; Live Now`;
                status.classList.add('text-danger');
                liveEvent = event;

                clone.getRootNode().firstChild.nextSibling.style.opacity = '100%'
            } else if (new Date(event.start_time) > now) {
                clone.getElementById('ifsc-starts-in').innerText = `⏰ ${pretty_starts_in(event)}`;
                status.innerHTML = `🔜 &nbsp; Upcoming`;
                status.classList.add('text-success');

                clone.getRootNode().firstChild.nextSibling.style.opacity = '50%'
            } else {
                clone.getElementById('ifsc-starts-in').innerText = `⏰ ${pretty_finished_ago(event)}`;
                status.innerHTML = `🏁 &nbsp; Finished`;
                status.classList.add('text-warning');

                clone.getRootNode().firstChild.nextSibling.style.opacity = '50%'
            }

            container.appendChild(clone);
        } catch (e) {
            console.log(e)
        }
    });

    if (liveEvent) {
        document.getElementById('next-event').innerHTML = `<p><strong>${nextEvent.description}</strong></p><div class="alert alert-danger" role="alert">🔴 Live Now: <strong>${liveEvent.name}</strong></div>`;
    } else {
        document.getElementById('next-event').innerHTML = `<p><strong>${nextEvent.description}</strong></p><div class="alert alert-success" role="alert">${pretty_starts_in(nextEvent)}: <strong>${nextEvent.name}</strong></div>`;
    }
});

(async () => {
    await refresh();
    window.setInterval(refresh, 1000 * 60);
})();
