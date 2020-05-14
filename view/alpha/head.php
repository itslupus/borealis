<head>
    <title>temp</title>
    <link href = 'view/alpha/style.css' rel = 'stylesheet'></link>
    <script>
        window.addEventListener('load', () => {
            let dropdowns = document.getElementsByClassName('dropdown-header');
            for (let dropdown of dropdowns) {
                dropdown.addEventListener('click', (el) => {
                    let content = el.target.nextElementSibling;
                    let content_classes = content.classList;

                    content_classes.toggle('hidden');
                });
            }
        });
    </script>
</head>