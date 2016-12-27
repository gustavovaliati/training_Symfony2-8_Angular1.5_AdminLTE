
class TicketCtrl {
  constructor(User, $sce, $rootScope) {
    'ngInject';

    this.currentUser = User.current;

    $rootScope.setPageTitle("Super Ticket Title");
  }

}


export default TicketCtrl;
