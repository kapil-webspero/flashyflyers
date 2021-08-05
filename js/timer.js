(function( $ ) {

    jQuery.fn.dateTimer = function( options ) {
    var defaults = {
      date:"",
      hours:0,
      endMessage:'Time Over',
      timeZone:'America/New_York',
      onTimeOver:function(){},
    };
    var settings = $.extend( {}, defaults, options );
    var timer = this;

    this.changeTimezone = function (date, ianatz) {
      var invdate = new Date(date.toLocaleString('en-US', {
        timeZone: ianatz
      }));
      return invdate;
    }

    this.configDate = function(){
      var dateTime = (settings.date).split(' ');
      var date = dateTime[0].split('-');
      var time = dateTime[1].split(':');

      var newdate = timer.changeTimezone(new Date(),settings.timeZone);
          newdate.setDate(date[0]);
          newdate.setMonth(date[1] - 1);
          newdate.setFullYear(date[2]);

          newdate.setHours(time[0]);
          newdate.setMinutes(time[1]);
          newdate.setSeconds(time[2]);
        
        return newdate;
    }
    
    this.calculateTimeFromDates = function(){
      var newdate = timer.configDate();
      var endTime = ((Date.parse(newdate) / 1000) + (settings.hours * 3600));
      var newDate = timer.changeTimezone(new Date(),settings.timeZone);
      var delta = (endTime - (newDate.getTime() / 1000));
      
      var days = 0; //Math.floor(delta / 86400);
      // delta -= days * 86400;

      var hours = Math.floor(delta / 3600);
      delta -= hours * 3600;
      hours  = hours < 0 ? 0 : hours;

      var minutes = Math.floor(delta / 60) % 60;
      delta -= minutes * 60;
      minutes = minutes < 0 ? 0 : minutes;
    
      var seconds = parseInt(delta % 60);  // in theory the modulus is not required
      seconds = seconds < 0 ? 0 : seconds;
      var newDate = timer.changeTimezone(new Date(),settings.timeZone);
      if((newDate.getTime()/1000) > endTime){return false;}
      return timer.timeLayout(days,hours,minutes,seconds);
    }

    this.timeLayout = function(days,hours,minutes,seconds){
      var html = '';
      
      if(days < 10){days = '0'+days}
      if(hours < 10){hours = '0'+hours}
      if(minutes < 10){minutes = '0'+minutes}
      if(seconds < 10){seconds = '0'+seconds}
      var label = days > 1 ? 'Days' : 'day';

      if(days > 0){
        html += '<div class="time-cell timer-day">';
          html += '<span>'+days+'</span><span>'+label+'</span>';
        html += '</div>';
      }

      html += '<div class="time-cell timer-hour">';
        label = hours > 1 ? 'Hours' : 'Hour';
      var hoursDigits = "<div class='timerLabel'>"+label+"</div>";
      var hoursLength = 12;
      for (var i = 0; i < hours.toString().length; i++) {
            hoursDigits +="<div class='digitWiseCount'>"+hours.toString().charAt(i)+"</div>";
      }
		
        html += '<div class="timerDigits">'+hoursDigits+'</div>';
      html += '</div>';

      html += '<div class="time-cell timer-minute">';
        label = minutes > 1 ? 'Minutes' : 'Minute';
        
      var minutesDigits = "<div class='timerLabel'>"+label+"</div>";
      for (var i = 0; i < minutes.toString().length; i++) {
            minutesDigits +="<div class='digitWiseCount'>"+minutes.toString().charAt(i)+"</div>";
      }
      html += '<div class="timerDigitsPoint">:</div><div class="timerDigits">'+minutesDigits+'</div>';
        html += '</div>';

        html += '<div class="time-cell timer-second">';
          label = seconds > 1 ? 'Seconds' : 'Second';
          var secondsDigits = "<div class='timerLabel'>"+label+"</div>";
      for (var i = 0; i < seconds.toString().length; i++) {
            secondsDigits +="<div class='digitWiseCount'>"+seconds.toString().charAt(i)+"</div>";
      }
      html += '<div class="timerDigitsPoint">:</div><div class="timerDigits">'+secondsDigits+'</div>';
        html += '</div>';
      
      return html;
    }

    this.runTimer = function(el){
      var newDate = timer.changeTimezone(new Date(),settings.timeZone);
      if(timer.configDate().getTime() > newDate.getTime()){return;}
      var interval = setInterval(function(){
        var time = timer.calculateTimeFromDates();
        if(!time){ clearInterval(interval); time = settings.endMessage;
          if(typeof settings.onTimeOver === 'function'){
            settings.onTimeOver();
          }
        }
        el.html(time);  
      }, 1000);
    }
    return this.each(function() {
      timer.runTimer($(this));
    });
  }; 
})( jQuery );