document.getElementById('answerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let answer = document.getElementById('answer').value;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "submit.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status == 200) {
            document.getElementById('feedback').textContent = xhr.responseText;
        } else {
            document.getElementById('feedback').textContent = 'An error occurred';
        }
    };

    xhr.send(`answer=${answer}`);
});
