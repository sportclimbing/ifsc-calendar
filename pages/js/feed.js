const RSS_URL = `feed/rss.xml`;
const container = document.getElementById("container");
const template = document.getElementById("news-item-template");

function html(el, selector) {
    let e = el.querySelector(selector);
    return e ? e.innerHTML : null;
}

fetch(RSS_URL)
    .then(response => response.text())
    .then(str => new window.DOMParser().parseFromString(str, "text/xml"))
    .then((data) => {
        data.querySelectorAll("item").forEach(el => {
            try {
                const clone = template.content.cloneNode(true);
                clone.getElementById('ifsc-title').innerText = html(el, "title");
                clone.getElementById('ifsc-title').href = html(el, "guid");
                clone.getElementById('ifsc-image').src = html(el, "description").match(/src="([^"]+)"/)[1];
                clone.getElementById('ifsc-date').innerText = new Date(html(el, "pubDate")).toLocaleString();

                container.appendChild(clone);
            } catch (e) {}
        })
    });