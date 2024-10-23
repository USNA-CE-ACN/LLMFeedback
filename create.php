<?php
	include('session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question and Answer Form</title>
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
	      <label for="name">Name: </label>
		  
<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL);
	
    $guid = $db->real_escape_string($_GET["guid"]);
    $sql = "SELECT * FROM Checkpoint WHERE guid = '$guid'";

    $result = $db->query($sql);
    $count = $result->num_rows;
	
    $name = "Not Found";
    $checkpoint_id = 0;

    if($count == 1) {
	$row = $result->fetch_row();
	$name = $row[1];
	$checkpoint_id = $row[0];		
    }

    echo "<input type=\"text\" name=\"name\" class=\"name\" value=\"" . $name . "\" required>";
    echo "<label for=\"url\">URL: </label>";
    echo "<input type=\"text\" name=\"url\" class=\"url\" value=\"" . $guid . "\" readonly>";
    echo "<input type=\"text\" name=\"checkpoint_id\" id=\"checkpoint_id\" value=\"" . $checkpoint_id . "\" style=\"display: none\">";
?>
	    </div>
	  </div>
	</section>
      </div>

        <div id="questionsContainer"></div>

	<div id="main">
	     <section class="main">

        <button type="button" onclick="addQuestion()">Add Question</button>
        <button type="submit">Save</button>
	</section>
	</div>
    </form>

  </div>

    <script>
        let questionCounter = 0;

        function addQuestion() {
            questionCounter++;
            const currentQuestionId = questionCounter; // Capture the current question ID

            const questionDiv = document.createElement('div');
            questionDiv.className = 'question';
            questionDiv.id = `question_${currentQuestionId}`;

	    const section = document.createElement('section');
	    section.className="main";
	    section.id="second";

	    const mainDiv = document.createElement('div');
	    mainDiv.id = 'main';
	    
            const questionLabel = document.createElement('label');
            questionLabel.textContent = `Question ${currentQuestionId}: `;
            section.appendChild(questionLabel);

            const questionInput = document.createElement('input');
            questionInput.type = 'text';
            questionInput.name = `questions[${currentQuestionId}][question]`;
            section.appendChild(questionInput);

            // Create the Question Type dropdown
            const questionTypeLabel = document.createElement('label');
            questionTypeLabel.textContent = ' Type: ';
            section.appendChild(questionTypeLabel);

            const questionTypeSelect = document.createElement('select');
            questionTypeSelect.name = `questions[${currentQuestionId}][type]`;
	    questionTypeSelect.onchange = function() {
                handleQuestionTypeChange(currentQuestionId, questionTypeSelect.value);
            };
	
            const option1 = document.createElement('option');
            option1.value = 'exact';
            option1.text = 'Text Exact Match';
            questionTypeSelect.appendChild(option1);

            const option2 = document.createElement('option');
            option2.value = 'contains';
            option2.text = 'Text Contains';
            questionTypeSelect.appendChild(option2);

            const option3 = document.createElement('option');
            option3.value = 'regex';
            option3.text = 'Text Regex';
            questionTypeSelect.appendChild(option3);

            const option4 = document.createElement('option');
            option4.value = 'mc';
            option4.text = 'Multiple Choice';
            questionTypeSelect.appendChild(option4);

			const option5 = document.createElement('option');
            option5.value = 'ms';
            option5.text = 'Multiple Select';
            questionTypeSelect.appendChild(option5);

            const option6 = document.createElement('option');
            option6.value = 'llm';
            option6.text = 'LLM Feedback';
            questionTypeSelect.appendChild(option6);
			
			const option7 = document.createElement('option');
            option7.value = 'ngt';
            option7.text = 'Numeric Greater';
            questionTypeSelect.appendChild(option7);

			const option8 = document.createElement('option');
            option8.value = 'nge';
            option8.text = 'Numeric Greater or Equal';
            questionTypeSelect.appendChild(option8);

			const option9 = document.createElement('option');
            option9.value = 'nlt';
            option9.text = 'Numeric Less';
            questionTypeSelect.appendChild(option9);

			const option10 = document.createElement('option');
            option10.value = 'nle';
            option10.text = 'Numeric Less or Equal';
            questionTypeSelect.appendChild(option10);
			
			const option11 = document.createElement('option');
            option11.value = 'ne';
            option11.text = 'Numeric Equal';
            questionTypeSelect.appendChild(option11);

			const option12 = document.createElement('option');
            option12.value = 'nne';
            option12.text = 'Numeric Not Equal';
            questionTypeSelect.appendChild(option12);

            section.appendChild(questionTypeSelect);

            const feedbackLabel = document.createElement('label');
            feedbackLabel.textContent = `Wrong Answer Feedback: `;
            section.appendChild(feedbackLabel);

            const questionFeedback = document.createElement('input');
            questionFeedback.type = 'text';
            questionFeedback.name = `questions[${currentQuestionId}][questionFeedback]`;
            section.appendChild(questionFeedback);

            const addAnswerButton = document.createElement('button');
            addAnswerButton.type = 'button';
            addAnswerButton.textContent = 'Add Answer';
            addAnswerButton.onclick = function() {
                addAnswer(currentQuestionId);
            };
            section.appendChild(addAnswerButton);

            const removeQuestionButton = document.createElement('button');
            removeQuestionButton.type = 'button';
            removeQuestionButton.textContent = 'Remove Question';
            removeQuestionButton.onclick = function() {
                removeQuestion(currentQuestionId);
            };
            section.appendChild(removeQuestionButton);

            const answersDiv = document.createElement('div');
            answersDiv.className = 'answers';
            answersDiv.id = `answers_${currentQuestionId}`;
            section.appendChild(answersDiv);

            const feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'feedbackOptions';
            feedbackDiv.id = `feedback_${currentQuestionId}`;
            section.appendChild(feedbackDiv);
	
	    mainDiv.appendChild(section);
	    questionDiv.appendChild(mainDiv);
            document.getElementById('questionsContainer').appendChild(questionDiv);
        }

	function handleQuestionTypeChange(questionId, questionType) {
            const feedbackDiv = document.getElementById(`feedback_${questionId}`);
            feedbackDiv.innerHTML = '';

            if (questionType === 'llm') {
                const thresholdLabel = document.createElement('label');
                thresholdLabel.textContent = ' Threshold: ';
                feedbackDiv.appendChild(thresholdLabel);

                const thresholdSelect = document.createElement('select');
                thresholdSelect.name = `questions[${questionId}][threshold]`;
                for (let i = 1; i <= 10; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.text = i;
                    thresholdSelect.appendChild(option);
                }
                feedbackDiv.appendChild(thresholdSelect);
            }else if (questionType === 'ne' || questionType === 'nne'){
				const marginLabel = document.createElement('label');
                marginLabel.textContent = ' Margin of Error: ';
                feedbackDiv.appendChild(marginLabel);

				const marginInput = document.createElement('input');
				marginInput.type = 'text';
				marginInput.name = `questions[${questionId}][margin]`;
				marginInput.value = '0';
                feedbackDiv.appendChild(marginInput);
			}
        }
	
        function addAnswer(questionId) {
            const answersDiv = document.getElementById(`answers_${questionId}`);
            const answerCounter = answersDiv.children.length + 1;
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer';
            answerDiv.id = `question_${questionId}_answer_${answerCounter}`;

			const answerId = document.createElement('input');
            answerId.type = 'text';
            answerId.name = `questions[${questionId}][answer_ids][]`;
			answerId.value = answerCounter;
			answerId.style.display = "none";
            answerDiv.appendChild(answerId);

            const answerLabel = document.createElement('label');
            answerLabel.textContent = `Answer ${answerCounter}: `;
            answerDiv.appendChild(answerLabel);

			const answerCorrect = document.createElement('input');
			answerCorrect.type = 'checkbox';
			answerCorrect.name = `questions[${questionId}][correct][]`;
			answerCorrect.checked = true;
			answerCorrect.value = answerCounter;
			answerDiv.appendChild(answerCorrect);

            const answerInput = document.createElement('input');
            answerInput.type = 'text';
            answerInput.name = `questions[${questionId}][answers][]`;
            answerDiv.appendChild(answerInput);

            const removeAnswerButton = document.createElement('button');
            removeAnswerButton.type = 'button';
            removeAnswerButton.textContent = 'Remove Answer';
            removeAnswerButton.onclick = function() {
                removeAnswer(questionId, answerCounter);
            };
            answerDiv.appendChild(removeAnswerButton);

            answersDiv.appendChild(answerDiv);
        }

        function removeQuestion(questionId) {
            document.getElementById(`question_${questionId}`).remove();
        }

        function removeAnswer(questionId, answerId) {
            document.getElementById(`question_${questionId}_answer_${answerId}`).remove();
        }

        document.getElementById('checkpointForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

	    for (const pair of formData.entries()) {
	      console.log(pair[0], pair[1]);
	      }

            fetch('save.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(data => alert(data))
              .catch(error => console.error('Error:', error));
        });
    </script>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
