(() => {
    let RSS_URL = `feed/rss.xml`;
    let container = document.getElementById("container");
    let template = document.getElementById("news-item-template");

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
                    let clone = template.content.cloneNode(true);
                    clone.getElementById('ifsc-title').innerHTML = html(el, "title");
                    clone.getElementById('ifsc-title').href = html(el, "guid");
                    clone.getElementById('ifsc-image').src = html(el, "description").match(/src="([^"]+)"/)[1];
                    clone.getElementById('ifsc-date').innerText += dayjs(html(el, "pubDate")).fromNow();

                    container.appendChild(clone);
                } catch (e) {
                }
            })
        });
})();
