const apiKey = 'AIzaSyC-EwtvF_rWHfDYm6fr_tcpn1NkI_wFMEs';
const channelId = 'UCzbu2l9o-a5Rh6Tbs9MIyHw';

async function fetchLatestVideos() {
    try {
        const response = await fetch(
            `https://www.googleapis.com/youtube/v3/search?key=${apiKey}
            &channelId=${channelId}
            &order=date
            &part=snippet
            &type=video
            &videoEmbeddable=true
            &videoDuration=medium
            &maxResults=10`
        );

        const data = await response.json();
        console.log(data); // ← check what it returns

        if (data.items && data.items.length > 0) {
            const videoId = data.items[0].id.videoId;
            document.getElementById("latest-video").src =
                `https://www.youtube-nocookie.com/embed/${videoId}?rel=0&modestbranding=1`;
        } else {
            console.error("No embeddable videos found.");
        }

    } catch (err) {
        console.error("Error fetching video:", err);
    }
}

fetchLatestVideos();
