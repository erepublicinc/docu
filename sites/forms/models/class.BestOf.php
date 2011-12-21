<?
class Bestof
{
    
    static function GetCurrentContest()
    {
        return new Query("SELECT * FROM bestof_contests WHERE  NOW() > contest_startdate AND NOW() < contest_enddate       ");
    } 
    
    static function GetEntriesForUser($email)
    {
        return new Query(" SELECT * FROM bestof_entries JOIN bestof_players  ON player_pk = entry_playerid  WHERE player_email = '$email'  ");
    } 
    
    static function GetAnswersForEntry($entry_id)
    {
        return new Query(" SELECT * FROM bestof_answers JOIN bestof_questions ON answer_questionid = question_pk  WHERE answer_entryId = '$entry_id' ");
    }
    
}
