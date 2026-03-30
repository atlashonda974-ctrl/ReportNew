@if(request()->is('r1'))
    <title>Get Request Note Report 1</title>
@elseif(request()->is('r2'))
    <title>Get Request Note Report 2</title>
@elseif(request()->is('register'))
    <title>Assets Register Report</title>
@elseif(request()->is('reports'))
    <title>Assets Addition Report</title>
@elseif(request()->is('transfer'))
    <title>Assets Transfer Report</title>
@elseif(request()->is('dep'))
    <title>Assets Deletion Report</title>
@elseif(request()->is('reinsurace'))
    <title>Reinsurance Report</title>
@elseif(request()->is('broker'))
    <title>Broker Report</title>
@elseif(request()->is('premium'))
    <title>Premium Outstanding Report</title>
@elseif(request()->is('renewal'))
    <title>Renewal Report</title>
@elseif(request()->is('user'))
    <title>User Report</title>
@elseif(request()->is('u_active'))
    <title>User status Report</title>
@else
    <title>Default Title</title>
@endif