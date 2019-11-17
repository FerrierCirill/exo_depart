function select() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", 'js/choix_js.json', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            
            obj = JSON.parse(xhr.responseText);
            for (let i = 0; i < obj.length; i++) {
                document.getElementById('choix').innerHTML += `<option value="${obj[i].value}">${obj[i].label}</option>`;             
            } 

        }
    }
    xhr.send();
}

select();


document.getElementById('btn').addEventListener("click", 
    function() {
        let name    = document.getElementById('name').value;
        let choix   = document.getElementById('choix').value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", 'http://miw.spipha.fr:3000/api/contest_js', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("name=" + name +"&choix="+ choix);

        alert('test');
    }
);

