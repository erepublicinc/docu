<div id="id_cc_form">

    <script type="text/javascript">
    {literal}
        validateCreditCard = function()
        {
            return false;
        }
    {/literal}
    </script>

    <h3>Credit Card Payment Information:</h3>
    <div class="formitem">
        <label for="credit_card_type">Card Type<span class=" red" > *</span></label> <br clear="all">  
        <select style="width: 216px; height: 21px;" name="credit_card_type" class="required" >
            <option value="">Select Card Type</option>
            <option value="Visa">Visa</option>
            <option value="MasterCard">MasterCard</option>
            <option value="American Express">American Express</option>
        </select> 
    </div> 
  
    <div class="formitem">
        <label for="credit_card_number">Card Number<span class="red" > *</span></label> <br clear="all">  
        <input type="text" name="credit_card_number" class="required" validation="credit_card_number"/> 
    </div>
  
    <div class="formitem">
        <label for="credit_card_exp_month">Expiration Date<span class="red" > *</span></label> <br clear="all"> 
        <select style="width: 115px; height: 21px;" name="credit_card_exp_month" class="required">
            <option value="">Select Month</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
    
        <select style="width: 90px; height: 21px;" name="credit_card_exp_year" class="required">
            <option value="">Select Year</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
        
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
        
            <option value="18">18</option>
        </select>
    </div>
  
    <div class="formitem">
        <label for="credit_card_security">Security Code<span class="red" > *</span></label> <br clear="all">  
        <input type="text" style="width: 50px;" name="credit_card_security" class="required" validation="3digits"/> 
        <a href="#" onclick="open(src= 'http://pages.erepublic.com/content/CreditCardSecurity','newwindow','width=500,height=400,scrollbars=yes');">
            <font size="-2" face="Arial">What is this?</font>
        </a>
    </div>
    
    <div class="formitem">
        <label for="credit_card_first_name">First Name on Card<span class="red" > *</span></label> <br clear="all"> 
        <input type="text" name="credit_card_first_name" class="required"/> 
    </div>
    
    <div class="formitem"> 
        <label for="credit_card_last_name">Last Name on Card<span class="red" > *</span></label> <br clear="all">
        <input type="text" name="credit_card_last_name" class="required"/>
    </div>
  
    <div class="formitem">
        <label for="credit_card_email">**Email Recipient:</label> <br clear="all">
        <input type="text" name="credit_card_email" validation="email"/>
    </div>
    <p> **If you wish to send the Credit Card receipt to a different email address than your registration confirmation, please enter it here. </p>
</div>