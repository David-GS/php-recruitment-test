document.addEventListener("DOMContentLoaded", (event) => {

    function assignVarnish(event)
    {
        var data = {
            varnish_id: event.target.getAttribute('data-varnish-id'),
            website_id: event.target.getAttribute('data-website-id'),
            checked: event.target.checked
        };

        fetch("/varnish/assign", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",                                                                                                
            },
            body:  JSON.stringify(data)
        })
        .then(res => res.json())
        .then(function(data){
            alert(data.message);
            // uncheck on faild
            if (!data.success) {
                event.target.checked = false;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    var assignCheckboxes = document.querySelectorAll('.varnish-website-assign');
    for (var i = 0; i < assignCheckboxes.length; i++) {
        assignCheckboxes[i].addEventListener('change', assignVarnish, false);
    }
});
