<?php
include('session.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

include_once('config.php');

$guid = $db->real_escape_string($_GET["guid"]);
$sql = "SELECT * FROM Checkpoint WHERE guid = '$guid'";
$result = $db->query($sql);
$count = $result->num_rows;

$name = "Not Found";
$checkpoint_id = 0;

if ($count == 1) {
    $row = $result->fetch_row();
    $name = $row[1];
    $checkpoint_id = $row[0];
}

$questions = [];
if ($checkpoint_id > 0) {
    $stmt = $db->prepare("SELECT * FROM Question WHERE checkpoint_id = ?");
    $stmt->bind_param("i", $checkpoint_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();
    while ($q = $questions_result->fetch_assoc()) {
        $qid = $q['question_id'];
        $ansStmt = $db->prepare("SELECT * FROM Answer WHERE question_id = ?");
        $ansStmt->bind_param("i", $qid);
        $ansStmt->execute();
        $ansRes = $ansStmt->get_result();
        $q['answers'] = $ansRes->fetch_all(MYSQLI_ASSOC);
        $ansStmt->close();
        $questions[] = $q;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Lab Checkpoint Question</title>
  <link rel="stylesheet" href="assets/css/main.css" />
  <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">
<div id="wrapper">
  <header id="header" class="alt">
    <h1>Create Lab Checkpoint Question</h1>
  </header>

  <form id="checkpointForm">
    <div id="main">
      <section id="intro" class="main">
        <div class="spotlight">
          <div class="content">
            <header class="major">
              <h2>Checkpoint Information and Description</h2>
            </header>
            <?php include("courses.php"); ?>
            <label for="name">Name: </label>
            <input type="text" name="name" class="name" value="<?php echo htmlspecialchars($name); ?>" required>
            <label for="url">URL: </label>
            <input type="text" name="url" class="url" value="<?php echo htmlspecialchars($guid); ?>" readonly>
            <input type="hidden" name="checkpoint_id" id="checkpoint_id" value="<?php echo $checkpoint_id; ?>">
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
const existingQuestions = <?php echo json_encode($questions); ?>;

window.onload = function () {
  for (const q of existingQuestions) {
    addQuestionFromData(q);
  }
};

function addQuestionFromData(data) {
  questionCounter++;
  const currentQuestionId = questionCounter;

  const questionDiv = document.createElement('div');
  questionDiv.className = 'question';
  questionDiv.id = `question_${currentQuestionId}`;

  const section = document.createElement('section');
  section.className = "main";
  const mainDiv = document.createElement('div');
  mainDiv.id = 'main';

  const questionLabel = document.createElement('label');
  questionLabel.textContent = `Question ${currentQuestionId}: `;
  section.appendChild(questionLabel);

  const questionInput = document.createElement('input');
  questionInput.type = 'text';
  questionInput.name = `questions[${currentQuestionId}][question]`;
  questionInput.value = data.question_text;
  section.appendChild(questionInput);

  const questionTypeLabel = document.createElement('label');
  questionTypeLabel.textContent = ' Type: ';
  section.appendChild(questionTypeLabel);

  const questionTypeSelect = document.createElement('select');
  questionTypeSelect.name = `questions[${currentQuestionId}][type]`;
  questionTypeSelect.onchange = function () {
    handleQuestionTypeChange(currentQuestionId, questionTypeSelect.value);
  };

  const types = ['exact', 'contains', 'regex', 'mc', 'ms', 'llm', 'ngt', 'nge', 'nlt', 'nle', 'ne', 'nne'];
  
  const typeToText = new Map();
  typeToText.set('exact','Text Exact Match');
  typeToText.set('contains','Text Contains');
  typeToText.set('regex','Text Regex');
  typeToText.set('mc','Multiple Choice');
  typeToText.set('ms','Multiple Select');
  typeToText.set('llm','LLM Feedback');
  typeToText.set('ngt','Numeric Greater');
  typeToText.set('nge','Numeric Greater or Equal');
  typeToText.set('nlt','Numeric Less');
  typeToText.set('nle','Numeric Less or Equal');
  typeToText.set('ne','Numeric Equal');
  typeToText.set('nne','Numeric Not Equal');
  
  for (const t of types) {
    const option = document.createElement('option');
    option.value = t;
    option.text = typeToText.get(t);
    if (t === data.question_type) option.selected = true;
    questionTypeSelect.appendChild(option);
  }
  section.appendChild(questionTypeSelect);

  const wrongDiv = document.createElement('div');
  wrongDiv.className = 'wrong';
  wrongDiv.id = `wrong_${currentQuestionId}`;
  const feedbackLabel = document.createElement('label');
  feedbackLabel.textContent = `Wrong Answer Feedback: `;
  wrongDiv.appendChild(feedbackLabel);
  const questionFeedback = document.createElement('input');
  questionFeedback.type = 'text';
  questionFeedback.name = `questions[${currentQuestionId}][questionFeedback]`;
  questionFeedback.value = data.feedback || '';
  wrongDiv.appendChild(questionFeedback);
  section.appendChild(wrongDiv);

  const addAnswerButton = document.createElement('button');
  addAnswerButton.type = 'button';
  addAnswerButton.textContent = 'Add Answer';
  addAnswerButton.onclick = function () {
    addAnswer(currentQuestionId);
  };
  section.appendChild(addAnswerButton);

  const removeQuestionButton = document.createElement('button');
  removeQuestionButton.type = 'button';
  removeQuestionButton.textContent = 'Remove Question';
  removeQuestionButton.onclick = function () {
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

  if (data.answers) {
    for (const a of data.answers) {
      addAnswerFromData(currentQuestionId, a);
    }
  }
  handleQuestionTypeChange(currentQuestionId, data.question_type);
}
function addAnswerFromData(questionId, data) {
  const answersDiv = document.getElementById(`answers_${questionId}`);
  const answerCounter = answersDiv.children.length + 1;

  const answerDiv = document.createElement('div');
  answerDiv.className = 'answer';
  answerDiv.id = `question_${questionId}_answer_${answerCounter}`;

  const answerId = document.createElement('input');
  answerId.type = 'hidden';
  answerId.name = `questions[${questionId}][answer_ids][]`;
  answerId.value = data.id;
  answerDiv.appendChild(answerId);

  const answerLabel = document.createElement('label');
  answerLabel.textContent = `Answer ${answerCounter}: `;
  answerDiv.appendChild(answerLabel);

  const answerCorrect = document.createElement('input');
  answerCorrect.type = 'checkbox';
  answerCorrect.name = `questions[${questionId}][correct][]`;
  answerCorrect.checked = data.correct == 1;
  answerCorrect.value = answerCounter;
  answerDiv.appendChild(answerCorrect);

  const answerInput = document.createElement('input');
  answerInput.type = 'text';
  answerInput.name = `questions[${questionId}][answers][]`;
  answerInput.value = data.answer_text;
  answerDiv.appendChild(answerInput);

  const removeAnswerButton = document.createElement('button');
  removeAnswerButton.type = 'button';
  removeAnswerButton.textContent = 'Remove Answer';
  removeAnswerButton.onclick = function () {
    removeAnswer(questionId, answerCounter);
  };
  answerDiv.appendChild(removeAnswerButton);

  answersDiv.appendChild(answerDiv);
}

function addQuestion() {
  addQuestionFromData({ question_text: '', question_type: 'exact', feedback: '', answers: [] });
}

function addAnswer(questionId) {
  const answersDiv = document.getElementById(`answers_${questionId}`);
  const answerCounter = answersDiv.children.length + 1;
  const answerDiv = document.createElement('div');
  answerDiv.className = 'answer';
  answerDiv.id = `question_${questionId}_answer_${answerCounter}`;

  const answerId = document.createElement('input');
  answerId.type = 'hidden';
  answerId.name = `questions[${questionId}][answer_ids][]`;
  answerId.value = answerCounter;
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
  removeAnswerButton.onclick = function () {
    removeAnswer(questionId, answerCounter);
  };
  answerDiv.appendChild(removeAnswerButton);

  answersDiv.appendChild(answerDiv);
}

function handleQuestionTypeChange(questionId, questionType) {
  const feedbackDiv = document.getElementById(`feedback_${questionId}`);
  feedbackDiv.innerHTML = '';
  const wrongDiv = document.getElementById(`wrong_${questionId}`);

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
    wrongDiv.style.display = 'none';
  } else {
    wrongDiv.style.display = 'block';
  }

  if (questionType === 'ne' || questionType === 'nne') {
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
