<!DOCTYPE html>
<html lang="en">
<head>
    <title>{$page_title}</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>

{literal}    
    <style>
    .red{
        color:red;
    }
    .invalid{
        color:red;
        border: solid red 3px;
    }
    </style>
    
    
    
    <script>
    $(document).ready(function() {
            
        $('input[type="submit"]').click(onSubmit);
        $('.date_type').datepicker();
        $('input[validation]').blur(validate);
        $('select[validation]').blur(validate);
    });
    
    validate = function()
    {
        var valid   = true;
        var valType = $(this).attr('validation');
        var value   = $(this).val();
        
        //alert(valType);
        if(value != '')
        {
            switch(valType)
            {
            case 'number':
                valid = /^\d+\s*$/.test(value);
                break;  
            case 'alphanumeric':
            	valid = /^[a-zA-Z]+/.test(value);
                break;  
            case 'credit_card_number':
            	 msg   = "must be 13-16 digits";
                 valid = /^[\d ]{13,19}\s*$/.test(value);
                break;  
            case '3digits':
                msg   = "must be 3 digits";
                valid = /^\d{3}\s*$/.test(value);
                break;          
            case 'phone':
            	 msg   = "must be 10 digits"; // 10 digits ,optional: spaces or periods, optional: x234
                 valid = /^\d{3}[\. ]?\d{3}[\. ]?\d{4}([\. ]?[xX]\d{2,4})?$/.test(value);
                break;
            case 'email':
            	msg   = "must be a valid mail address";
                valid = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6}/.test(value);
                break;        
            }
        }
        
        if(! valid)
        {
            $(this).addClass('invalid');
        }
        else
        { 
        	$(this).removeClass('invalid');
        }
    }


    defaultOnSubmit = function(){
        alert('standard onsubmit validation');
    }; 

    
    var onSubmitFunctions = [defaultOnSubmit];
    onSubmit = function()
    {
        // call all functions on the stack
        for( var i in  onSubmitFunctions)
        {
        	onSubmitFunctions[i]();
        }          
    };
/*
    // extra onsubmit validation
    onSubmitFunctions.push(function(){
          alert('more  onsubmit validation');
    });  
*/    
   
    </script>
{/literal}  
</head>

<body>


     
    <div >
         <form id="id_form"  method="post">               
            <fieldset>                      
            {foreach $form_data as $field_id => $field}
                <div class=formitem>                
                {if $field.type == 'submit'}
                    <input type="submit" name="{$field.html_name}" style="float:left" value="{$field.label}">
                {elseif $field.type == 'template'}
                    {include file= $field.tpl }                                                                                                       
                {elseif $field.label}                        
                    <label class="grid_12">{$field.label}    {if $field.required} <span class="red" > *</span>{/if}</label> 
                    <br clear="all">                  
                {/if}       
                {if $field.type == 'html'} 
                    {$value.$field_name}                                       
                {elseif $field.type == 'hidden'} 
                    <input type="hidden" name="{$field.html_name}" value="{$value.$field_name}" />                        
                {elseif $field.type == 'divider'} 
                    <hr />                            
                {elseif $field.type == 'textarea'}                         
                    <textarea id="id_body" type="text" name="{$field.html_name}" rows="25" class="{if $field.required} required{/if}" {if $field.validation}validation="{$field.validation}"{/if}>{$value.$field_name}</textarea>                       
                {elseif $field.type == 'checkbox'}
                    <input name="{$field.html_name}" type="checkbox" {if $value.field_name == 1} selected="selected"{/if} {if $field.validation}validation="{$field.validation}"{/if}/>                        
                {elseif $field.type == 'radiobutton'}
                    <input name="{$field.html_name}" type="radiobutton" {if $value.field_name == 1} selected="selected"{/if} {if $field.validation}validation="{$field.validation}"{/if}/>                           
                {elseif $field.form_element == 'select'}                        
                    <select  name="{$field.html_name}" class="{if $field.required} required{/if}" {if $field.validation}validation="{$field.validation}"{/if}>
                       {foreach $field.options as $option}
                             <option value="{$option->id}" {if $option->id == $value.$field_name} selected="selected"{/if} >{$option->title}</option>                            
                       {/foreach}
                    </select>                                                   
                {elseif $field.type == 'text'}
                    <input type="text" name="{$field.html_name}" class="{if $field.required} required{/if}" value="{$value.$field_name}" {if $field.validation}validation="{$field.validation}"{/if}>                           
                {elseif $field.type == 'datetime'}
                    <input type="text" name="{$field.html_name}" class="date_type {if $field.required} required{/if}" value="{$value.$field_name|date_format:$DATETIME_FORMAT}" {if $field.validation}validation="{$field.validation}"{/if}>                                                                                                                                             
                {/if}
                </div>
            {/foreach}                        
            </fieldset>                   
        </form>       
    </div> 
     
</body>
</html>
  
  
  
  
  
  
  

