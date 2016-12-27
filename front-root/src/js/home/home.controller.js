class HomeCtrl {
  constructor(User, AppConstants, $scope) {
    'ngInject';

    this.appName = AppConstants.appName;
    this._$scope = $scope;

    this.logout = User.logout.bind(User);

  }



}

export default HomeCtrl;
