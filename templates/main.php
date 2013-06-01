{{ script('vendor/angular/angular', 'appframework') }}
{{ script('public/app', 'appframework') }}
{{ script('vendor/angular/angular-resource') }}
{{ script('vendor/angular-ui/angular-ui') }}
{{ script('vendor/select2/select2') }}
{{ script('public/app') }}
{{ style('style') }}
{{ style('animation') }}
{{ style('vendor/select2') }}

<div id="perlenbilanz"
	 ng-app="perlenbilanz">

	<div ng-view></div>

</div>



