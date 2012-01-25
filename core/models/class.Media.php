<?
class Media extends Model
{
        
    protected static $mContentFieldDescriptions = array(
            'contents_clk_id'          => array('type'=>'int', 'insert_only'=>true),
            'contents_id'              => array('type'=>'int', 'insert_only'=>true),
            'contents_title'           => array('type'=>'varchar', 'label'=>'Title', 'required'=>true),
            'contents_display_title'   => array('type'=>'varchar', 'label'=>'Display title'),
            'contents_create_date'     => array('type'=>'datetime', 'insert_only'=>true,'do_not_validate'=>true),  // NOW()
          //  'contents_mod_date'     => array('type'=>'datetime', 'do_not_validate'=>true),  // NOW()   updated by system
            'contents_pub_date'        => array('type'=>'datetime', 'label'=>'Publication Date' ), 
            'contents_type'            => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_summary'         => array('type'=>'varchar', 'label'=>'Summary'),
            'contents_url_name'        => array('type'=>'varchar', 'label'=>'URL Name'),
            'contents_mod_users_id'    => array('type'=>'int', 'required'=>true),   
            'contents_authors_id'      => array('type'=>'int', 'label'=>'Author', 'form_element' =>'select'),   
            'contents_extra_table'     => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_live_rev'        => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newrev
            'contents_preview_rev'     => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newrev
            'contents_latest_rev'      => array('type'=>'int', 'do_not_validate'=>true)    // could be  @newrev
            );
    
    
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
    
    public static function listMedia()
    {
        $media_id    = intval($media_id);
        $contents_id  = intval($contents_id);
        $sql = "SELECT * FROM media ORDER BY  DESC LIMIT 50";
        
        return new Query($sql);
    }
    
}