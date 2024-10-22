<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Lab Checkpoint Question</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
  <div id="wrapper">
    <header id="header" class="alt">
      <h1>Create Lab Checkpoint Question</h1>
    </header>
    <form id="checkpointForm">
      <div id ="main">
	<section id="intro" class="main">
	  <div class="spotlight">
	    <div class="content">
	      <header class="major">
		<h2>Checkpoint Information and Description</h2>
	      </header>
	      <?php include("courses.php"); ?>
	      <label for="description">Description: </label>
	      <input type="text" name="description" class="description" required>
	    </div>
	  </div>
	</section>
      </div>

      <br />
      
      <div id="questionsContainer">
	<div class="question">
	  <div id="main">
	    <section id="second" class="main">
              <label for="question_type">Question Type:</label>
              <select name="question_type" class="question_type" required>
		<option value="text_validation">Text Validation</option>
		<option value="llm">Large Language Model Feedback</option>
		<option value="multiple_choice">Multiple Choice</option>
              </select>
              
              <label for="question_text">Question:</label>
              <input type="text" name="question_text[]" class="question_text" required>

	      <div id="answersContainer">
		<div class="answer">
		  <label for="validation">Sample Answer:</label>
		  <input type="text" name="validation[]" class="validation" required>
		</div>
	      </div>
            
              <div class="options_container" style="display: none;">
		<label for="options">Options (comma separated):</label>
		<input type="text" name="options[]" class="options">
              </div>

	      <button type="button" class="add_answer">Add Answer</button>
              <button type="button" class="remove_question">Remove</button>
	    </section>
          </div>
	  <br />
	</div>
      </div>

      <div id="main">
	<section class="main">
	  <button type="button" id="addQuestion">Add Another Question</button>
	  <button type="button" id="save">Save</button>
	  <br />
	</section>
      </div>

    <script>
      document.getElementById('addQuestion').addEventListener('click',
							      function() {
          let questionsContainer = document.getElementById('questionsContainer');
          let newQuestion = document.querySelector('.question').cloneNode(true);
          newQuestion.querySelector('.question_text').value = '';
	  newQuestion.querySelector('.validation').value = '';
          newQuestion.querySelector('.options').value = '';
          newQuestion.querySelector('.options_container').style.display = 'none';
          questionsContainer.appendChild(newQuestion);
      });

      document.getElementById('save').addEventListener('click',
       function(){

       });
      
      document.getElementById('questionsContainer').addEventListener('click', function(e) {
          if (e.target.classList.contains('remove_question')) {
              e.target.closest('.question').remove();
          }
      });

      document.getElementById('questionsContainer').addEventListener('click', function(e) {
	  if (e.target.classList.contains('add_answer')) {
	      let answersContainer = document.getElementById('answersContainer');
              let newAnswer = document.querySelector('.answer').cloneNode(true);
	      newAnswer.querySelector('.validation').value = '';
              answersContainer.appendChild(newAnswer);
          } 
      });
      
      document.getElementById('questionsContainer').addEventListener('change', function(e) {
          if (e.target.classList.contains('question_type')) {
              let optionsContainer = e.target.closest('.question').querySelector('.options_container');
              if (e.target.value === 'multiple_choice') {
                  optionsContainer.style.display = 'block';
              } else {
                  optionsContainer.style.display = 'none';
              }
          }
      });
    </script>
    
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    </form>
  </div>
</body>
</html>

