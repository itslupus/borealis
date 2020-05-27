<script>
    window.addEventListener('load', () => {
        let xhr = new XMLHttpRequest();
    
        let grade_div = document.getElementById('grades');
        grade_div.textContent = 'loading';

        xhr.onreadystatechange = () => {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                switch (xhr.status) {
                    case 200:
                        grade_div.textContent = xhr.responseText;

                        break;
                    case 405:
                        p.textContent = 'invalid method';
                        break;
                    case 403:
                        p.textContent = 'invalid request';
                        break;
                }
            }
        };

        xhr.open('POST', '../logic/FetchGrade.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('&term=202010');
    });
</script>

<p>grade view</p>
<div class = 'dropdown'>
    <div class = 'dropdown-header padded'><?=$term?></div>
    <div class = 'dropdown-content'>
        <div id = 'grades'></div>
    </div>
</div>