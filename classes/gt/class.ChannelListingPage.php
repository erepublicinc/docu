<?php


class ChannelListingPage extends WebPage
{
    
    public function __construct($data)
    {
        parent::__construct($data);
    }
    
    public function Render(){
        $contents = Content::GetPageContents(); 
    }
}


