import angular from 'angular';

// Create the module where our functionality can attach to
let ticketModule = angular.module('app.ticket', []);

// Include our UI-Router config settings
import TicketConfig from './ticket.config';
ticketModule.config(TicketConfig);


// Controllers
import TicketCtrl from './ticket.controller';
ticketModule.controller('TicketCtrl', TicketCtrl);



export default ticketModule;
