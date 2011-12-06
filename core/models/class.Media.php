<?
class Media
{

    public function __construct()
    {
        
    }
    
    public static function getMediaForContent($contents_fk)
    {
        $contents_fk  = intval($contents_fk);
        $sql = "SELECT * FROM  (media JOIN media__contents ON media_pk = media_fk) 
                         JOIN contents on contents_pk contents_fk
                         WHERE contents_fk = $contents_fk";
        
        return new Query($sql);
    }
    
    public static function linkMediaToContent($media_fk, $contents_fk)
    {
        $media_fk    = intval($media_fk);
        $contents_fk  = intval($contents_fk);
        $sql = "INSERT INTO media__contents (media_fk, contents_fk) VALUES($media_fk, $contents_fk)";
        
        return new Query($sql);
    }
    
}