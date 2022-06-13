/*START VDZ Google Analytics Plugin CF7 EVENTS*/
//Send request
document.addEventListener( 'wpcf7submit', function( event ) {
    event.detail.contactFormId;
    //console.log(event.detail);
    //ADD Events to Google Analytics
    var obj_form = {
        hitType: 'event',
        eventCategory: 'VDZ_GA_SEND_CF7',
        eventAction: 'SEND_REQUEST',
        eventLabel: 'VDZ_GA_SEND_CF7_'+event.detail.contactFormId,
    };
    var send = false;
    if(window.gtag){
        gtag('event', obj_form.eventAction, { 'event_category': obj_form.eventCategory, 'event_action': obj_form.eventAction, 'event_label': obj_form.eventLabel});
        console.log('GTAG send = ',obj_form);
        send = true;
    }
    if(!send){
        if(window.ga){
            ga('send', obj_form);
            console.log('GA send = ',obj_form);
            send = true;
        }
    }
}, false );
//MailSend
document.addEventListener( 'wpcf7mailsent', function( event ) {
    event.detail.contactFormId;
    //console.log(event.detail);
    //ADD Events to Google Analytics
    var obj_form = {
        hitType: 'event',
        eventCategory: 'VDZ_GA_SEND_CF7',
        eventAction: 'SEND_MAIL',
        eventLabel: 'VDZ_GA_SEND_CF7_'+event.detail.contactFormId,
    };
    var send = false;
    if(window.gtag){
        gtag('event', obj_form.eventAction, { 'event_category': obj_form.eventCategory, 'event_action': obj_form.eventAction, 'event_label': obj_form.eventLabel});
        console.log('GTAG send = ',obj_form);
        send = true;
    }
    if(!send){
        if(window.ga){
            ga('send', obj_form);
            console.log('GA send = ',obj_form);
            send = true;
        }
    }
}, false );
//Error
document.addEventListener( 'wpcf7invalid', function( event ) {
    event.detail.contactFormId;
    //console.log(event.detail);
    //ADD Events to Google Analytics
    var obj_form = {
        hitType: 'event',
        eventCategory: 'VDZ_GA_SEND_CF7',
        eventAction: 'SEND_ERROR',
        eventLabel: 'VDZ_GA_SEND_CF7_'+event.detail.contactFormId,
    };
    var send = false;
    if(window.gtag){
        gtag('event', obj_form.eventAction, { 'event_category': obj_form.eventCategory, 'event_action': obj_form.eventAction, 'event_label': obj_form.eventLabel});
        console.log('GTAG send = ',obj_form);
        send = true;
    }
    if(!send){
        if(window.ga){
            ga('send', obj_form);
            console.log('GA send = ',obj_form);
            send = true;
        }
    }
}, false );
/*END VDZ Google Analytics Plugin CF7 EVENTS*/

