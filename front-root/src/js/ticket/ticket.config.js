function TicketConfig($stateProvider) {
  'ngInject';

  $stateProvider
  .state('app.home.ticket', {
    url: '/ticket',
    controller: 'TicketCtrl',
    controllerAs: '$ctrl',
    templateUrl: 'ticket/ticket.html',

    title: 'Ticket'
  });

};

export default TicketConfig;
