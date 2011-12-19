<?
class Media
{

    public function __construct()
    {
        
    }
    
    public static function getMediaForContent($contents_id)
    {
        $contents_id  = intval($contents_id);
        $sql = "SELECT * FROM  (media JOIN media__contents ON media_id = media_id) 
                         JOIN contents on contents_id contents_id
                         WHERE contents_id = $contents_id";
        
        return new Query($sql);
    }
    
    public static function linkMediaToContent($media_id, $contents_id)
    {
        $media_id    = intval($media_id);
        $contents_id  = intval($contents_id);
        $sql = "INSERT INTO media__contents (media_id, contents_id) VALUES($media_id, $contents_id)";
        
        return new Query($sql);
    }
    
}