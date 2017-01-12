<?php
if(is_float($_SESSION['course_step']+0))
    $step = $_SESSION['course_step']+2;
else
    $step = $_SESSION['course_step']+1;

switch ((int)$_SESSION['course_step']) {
    case 1:{
        echo '<h1 class="quiz_header">Kurs 1 – Einführung in die Welt der südafrikanischen Weine</h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 1“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 1 > </button>';
        echo do_shortcode('[real3dflipbook id="1"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 1 > </button>';
        break;
    }
    case 4:{
        echo '<h1 class="quiz_header">Kurs 2 – Weißweinstile</h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 2“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 2 > </button>';
        echo do_shortcode('[real3dflipbook id="2"]');
        echo '<button data-step="'.$step.'"class="next-quiz-step quiz-step">Starte Test 2 > </button>';
        break;
    }
    case 7:{
        echo '<h1 class="quiz_header">Kurs 3 – Rotweinstile </h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 3“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 3 > </button>';
        echo do_shortcode('[real3dflipbook id="3"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 3> </button>';
        break;
    }
    case 10:{
        echo '<h1 class="quiz_header">Kurs 4 – vom Küstenklima beeinflusste Weinbauregionen</h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 4“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 4 > </button>';
        echo do_shortcode('[real3dflipbook id="4"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 4 > </button>';
        break;
    }
    case 13:{
        echo '<h1 class="quiz_header">Kurs 5 – Weinregionen im Inland</h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 5“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 5 > </button>';
        echo do_shortcode('[real3dflipbook id="5"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 5 > </button>';
        break;
    }
    case 16:{
        echo '<h1 class="quiz_header">Kurs 6 – Andere beeinflussende Faktoren rund um die Produktion und den Wein</h1>';
        echo '<h4>Laden Sie sich die Kursunterlagen als PowerPoint-Präsentation herunter. Wenn Sie bereit sind klicken Sie bitte auf den Link „Starte Test 6“ um zu beginnen. </h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 6 > </button>';
        echo do_shortcode('[real3dflipbook id="6"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Starte Test 6 > </button>';
        break;
    }
}
?>