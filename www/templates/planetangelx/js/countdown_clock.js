nextPartyDate = new Date();
function countdown_nextparty()  {                               
    // define next party dates of form (Year, Month-1, Day of month, hr, min, sec, ms)
    // NOTE THAT MONTHS ARE ZERO INDEXED - ie, jan=0, feb=1, mar=3 etc etc
    partyDates = new Array(2);
    partyDates[0] = new Date(2010, 04, 28, 23, 0, 0, 0);
    partyDates[1] = new Date(2007, 11, 31, 22, 0, 0, 0);
    // work out which is the next date
    today = new Date();
    // use the below to change the "today" date for testing it picks the correct party      
    //today = new Date(2006, 1, 25, 11, 0, 0, 0);
    difference = 1;
    // go backwards through the array to find party closest to this one         
    for (i = (partyDates.length-1); i >= 0; i--) {
        difference = today - partyDates[i];
        if (difference < 0) {
            nextPartyDate = partyDates[i];
        }
    }
    countdown_clock(1);
}
function countdown_clock(format) {          
    //I chose a div as the container for the timer, but
    //it can be an input tag inside a form, or anything
    //who's displayed content can be changed through
    //client-side scripting.
    html_code = '<div id="countdown"></div>';
    html_code2 = '<div id="countdown_shadow"></div>';
    document.write(html_code);
    document.write(html_code2);
    countdown(format);
}
function countdown(format) {
    today = new Date();
    difference = Math.floor((nextPartyDate.getTime() - today.getTime()) / 1000);
    delete today;
    if(difference < 0) {
        difference  = 0;
    }
    switch(format) {    
            case 0:
                //The simplest way to display the time left.                     
                document.getElementById("countdown").innerHTML = difference  + ' seconds';
                document.getElementById("countdown_shadow").innerHTML = difference  + ' seconds';
                break;
            case 1:                     
                //More detailed.
                days = Math.floor(difference/86400);
                //days          
                difference  = difference % 86400;
                hours = Math.floor(difference/3600);
                //hours             
                difference  = difference % 3600;
                mins = Math.floor(difference/60);
                //minutes           
                difference  = difference % 60;
                secs = Math.floor(difference);
                //seconds                                          
                out = ' the next Party...<BR> is in ';
                if(days != 0) {
                    out += days +" day"+((days!=1)?"s":"")+", ";
                }
                if(days != 0 || hours != 0) {
                    out += hours +" hour"+((hours!=1)?"s":"")+", ";
                }
                if(days != 0 || hours != 0 || mins != 0) {
                    out += mins +" minute"+((mins!=1)?"s":"")+", ";
                }
                if (out != '   ... is in') {
                    out += ' and ';
                }
                out += secs +" seconds!";
                document.getElementById('countdown').innerHTML=out;
                document.getElementById('countdown_shadow').innerHTML=out;
                }
                //Recursive call, keeps the clock ticking.          
                setTimeout('countdown(' + format + ');
                ', 1000);
}
