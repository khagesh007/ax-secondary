<?php

function checkQuestionAttemptStatus($current_question,$attempt_answers) : string
 {

    try{
        $status = '';
        $question = $attempt_answers[$current_question->id] ?? '';
        if(count($attempt_answers) > 0 && $question != ''){
            if($attempt_answers[$current_question->id]?->is_skipped == 1 || $attempt_answers[$current_question->id]?->is_skipped == '1'){
                $status = 'skipped';
            }
            if($attempt_answers[$current_question->id]?->is_correct == 1 || $attempt_answers[$current_question->id]?->is_correct == '1'){
                $status = 'correct';
            }
            if($attempt_answers[$current_question->id]?->is_correct == 2 || $attempt_answers[$current_question->id]?->is_correct == '2'){
                $status = 'incorrect';
            }
            return $status;
        }else{
            return '-';
        }
        }catch (\Throwable $th) {
            //\Log::info("checkQuestionAttemptStatus function has error on QuizHelper.php file");
        return '';
        }
}

function checkIfItsCorrect($option,$attempt_answers,$question_id) : string
{
    try {


    // echo '<br>';
    // echo "attempt_answers ".$attempt_answers[$question_id]->quiz_q_answer_id.'<br>';
    // echo "Option id ".$option->id.'<br>';
    // echo "is it correct ".$option->is_correct.'<br>';
    // echo '<pre>';
    // print_r($attempt_answers[1]->quiz_q_answer_id);
    // echo '</pre>';
    // exit();
        $class = 'bg-light';
    /*  @if($answers->is_correct === 1 && $attempt_answers[$qstn->id]->quiz_q_answer_id === $answers->id)
                                                            bg-soft-success
                                                            @elseif($answers->is_correct === 0 && $attempt_answers[$qstn->id]->quiz_q_answer_id === $answers->id)
                                                             bg-soft-danger
                                                              @elseif($answers->is_correct === 1)
                                                             bg-soft-success
                                                             @else
                                                             bg-light
                                                              @endif

                                                              */
                                                              $question = $attempt_answers[$question_id] ?? '';
       $quiz_q_answer_id =  $question !='' ?  $attempt_answers[$question_id]?->quiz_q_answer_id : '';
    if(count($attempt_answers) > 0){
        if($option->is_correct === 1 && ($quiz_q_answer_id == $option->id)){
            $class = 'bg-soft-success';
        }elseif($option->is_correct === 0 && $quiz_q_answer_id === $option->id){
            $class = 'bg-soft-danger';
        }elseif($option->is_correct === 1)
            $class = 'bg-soft-success';
        }else{
            $class = 'bg-light';
        }

    } catch (\Throwable $th) {
      //  \Log::info("checkIfItsCorrect function has error on QuizHelper.php file");
    }
    return $class;
}
