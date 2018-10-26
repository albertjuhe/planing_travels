var app = angular.module('ShowTravelsApp', []);

function TravelController($scope,$http) {

    $scope.publishStateTravel = function (slug) {
        alert(slug);
        $http.get("http://travels.localhost/travel/publish/{slug}").then(function(response)
            {
                $scope.public = response.data;
            }
        )
    }
}

app.controller('travelController', TravelController);