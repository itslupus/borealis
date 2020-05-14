function createTable(json) {
    let tables = [];

    let parsed_json = JSON.parse(json);
    
    Object.keys(parsed_json).forEach((k) => {
        let section = parsed_json[k];
    
        let table = document.createElement('table');
        let table_body = document.createElement('tbody');

        table.setAttribute('border', 1);

        /*
            create table headers
        */
        let tr = document.createElement('tr');
        
        let td = document.createElement('td');
        td.textContent = 'sec';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'crn';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'cap';
        tr.appendChild(td);
        
        td = document.createElement('td');
        td.textContent = 'cnt';
        tr.appendChild(td);
        
        td = document.createElement('td');
        td.textContent = 'rem';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'wl_cap';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'wl_cnt';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'wl_rem';
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = 'instr';
        tr.appendChild(td);

        table_body.appendChild(tr);

        /*
            create table info
        */
        tr = document.createElement('tr');

        td = document.createElement('td');
        td.textContent = k;
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['crn'];
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['cap'];
        tr.appendChild(td);
        
        td = document.createElement('td');
        td.textContent = section['cnt'];
        tr.appendChild(td);
        
        td = document.createElement('td');
        td.textContent = section['rem'];
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['wl_cap'];
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['wl_cnt'];
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['wl_rem'];
        tr.appendChild(td);

        td = document.createElement('td');
        td.textContent = section['instr'];
        tr.appendChild(td);

        table_body.appendChild(tr);

        /*
            description row
        */
        tr = document.createElement('tr');

        td = document.createElement('td');
        td.textContent = section['desc'];
        td.setAttribute('colspan', 9);
        tr.appendChild(td);

        table_body.appendChild(tr);

        /*
            meet times
        */
        Object.keys(section['meets']).forEach((k2) => {
            let meet_time = section['meets'][k2];

            tr = document.createElement('tr');
            Object.keys(meet_time).forEach((k3) => {
                td = document.createElement('td');
                td.textContent = meet_time[k3];
    
                tr.appendChild(td);
            });

            table_body.appendChild(tr);
        });

        table.appendChild(table_body);

        tables.push(table);
    });

    return tables;
}