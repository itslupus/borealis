<script src = 'view/alpha/FetchCourse.js'></script>
<script>
    window.addEventListener('load', () => {
        let dropdown_course_info = document.querySelectorAll('div.dropdown-header[course]');
        for (let fetch of dropdown_course_info) {
           fetch.addEventListener('click', (el) => {
                let content = el.target.nextElementSibling;
                let p = content.children[0];

                if (p.textContent === '' && p.textContent !== 'loading') {
                    let course_code = el.target.getAttribute('course');
                    let term = el.target.getAttribute('term');

                    let xhr = new XMLHttpRequest();

                    p.textContent = 'loading';

                    xhr.onreadystatechange = () => {
                        if (xhr.readyState == XMLHttpRequest.DONE) {
                            switch (xhr.status) {
                                case 200:
                                    p.remove();

                                    for (let table of createTable(xhr.responseText)) {
                                        content.appendChild(table);
                                    }

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

                    xhr.open('POST', '../logic/FetchClass.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('course_code=' + course_code + '&term=' + term);
                }
            });
        }

        function delay(func, dly) {
            let time = 0;
            return (...orig) => {
                clearTimeout(time);

                time = setTimeout(func.bind(this, ...orig), dly || 0);
            };
        }

        let search = document.getElementById('input');
        search.addEventListener('input', delay((evnt) => {
            let query = evnt.target.value;

            let xhr = new XMLHttpRequest();
            xhr.onreadystatechange = () => {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    switch (xhr.status) {
                        case 200:
                            let parsed = JSON.parse(xhr.responseText);
                            
                            let list_div = document.getElementById('input-result-list');
                            list_div.innerHTML = '';

                            let list = document.createElement('ul');
                            list.setAttribute('class', 'tmp');
                            list_div.appendChild(list);
                            
                            for (crse of parsed) {
                                Object.keys(crse).forEach(key => {
                                    let list_item = document.createElement('li');
                                    list_item.setAttribute('course', key);
                                    list_item.textContent = crse[key];

                                    list_item.addEventListener('click', (evnt) => {
                                        let el = evnt.target;

                                        let form = document.getElementById('search');

                                        let input = document.createElement('input');
                                        input.setAttribute('type', 'hidden');
                                        input.setAttribute('name', 'courses[]');
                                        input.setAttribute('value', el.getAttribute('course'));

                                        document.getElementById('input').value = '';
                                        document.getElementById('input-result-list').innerHTML = '';

                                        form.appendChild(input);
                                        document.getElementById('input-result').textContent += el.textContent + ' | ';
                                    });

                                    list.appendChild(list_item);
                                });
                            }
                            
                            break;
                        case 405:
                            p.textContent = 'invalid method';
                            break;
                    }
                }
            };

            xhr.open('POST', '../logic/FilterSubject.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('name=' + query);
        }, 500));
    });
</script>

<p>search page</p>
<?php if($method === 'GET'){ ?>
    <input id = 'input' name = 'search' type = 'text' placeholder = 'course' autocomplete = 'off'></input><br>
    <p id = 'input-result'></p><br>
    <div id = 'input-result-list'></div>
    <form id = search action = 'search.php' method = POST autocomplete = off>
        <input type = submit value = go>
        <select name = 'term'>
            <?php foreach($terms as $term){ ?>
                <option value = '<?=$term?>'><?=$term?></option>
            <?php } ?>
        </select>
    </form>
<?php }else{ ?>
    <?php foreach($query_data as $k=>$subj){ ?>
        <div class = 'dropdown'>
            <div class = 'dropdown-header padded'><?=$k?></div>
            <div class = 'dropdown-content hidden'>
                <?php if(count($subj) === 0) { ?>
                    <p>none found for this term</p>
                <?php } ?>
                <?php foreach($subj as $j=>$course){ ?>
                    <div class = 'dropdown'>
                        <div class = 'dropdown-header padded' course = '<?=$j?>' term = '<?=$term?>'><?=$course?></div>
                        <div class = 'dropdown-content hidden'><p></p></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>