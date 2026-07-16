document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('location-detector').onclick = (event) => {
        const dataset = event.currentTarget.dataset;

        event.preventDefault();

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById(dataset.inputLatitude).value = Number(pos.coords.latitude.toFixed(6));
                document.getElementById(dataset.inputLongitude).value = Number(pos.coords.longitude.toFixed(6));
            },
            (err) => {
                console.error(err);
                document.getElementById(dataset.error).style.display = 'initial';
            },
        )
    };
});
