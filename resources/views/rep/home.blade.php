@extends('rep/layouts.app')

@section('content')
<div class="content-wrapper">
  <div class="row">
    @if($data['company'] != -1)
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
      <div class="card card-statistics">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center">
            <div class="highlight-icon bg-light mr-3">
              <i class="mdi mdi-widgets text-dark icon-lg"></i>
            </div>
            <div class="wrapper">
              <p class="card-text mb-0 theme-color">Total Companies</p>
              <div class="fluid-container">
                <h3 class="card-title mb-0">{{$data['company']}}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
      <div class="card card-statistics">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center">
            <div class="highlight-icon bg-light mr-3">
              <i class="mdi mdi-tag-multiple text-dark icon-lg"></i>
            </div>
            <div class="wrapper">
              <p class="card-text mb-0">Total Categories</p>
              <div class="fluid-container">
                <h3 class="card-title mb-0">{{$data['categories']}}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
      <div class="card card-statistics">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center">
            <div class="highlight-icon bg-light mr-3">
              <i class="mdi mdi-account-multiple text-danger icon-lg"></i>
            </div>
            <div class="wrapper">
              <p class="card-text mb-0 theme-color">Total Employees</p>
              <div class="fluid-container">
                <h3 class="card-title mb-0">{{$data['employee']}}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
      <div class="card card-statistics">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center">
            <div class="highlight-icon bg-light mr-3">
            <i class="mdi mdi-airballoon text-info icon-lg"></i>
            </div>
            <div class="wrapper">
              <p class="card-text mb-0 theme-color">Total Builds</p>
              <div class="fluid-container">
                <h3 class="card-title mb-0">{{$data['builds']}}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
      <div class="card card-statistics">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-center">
            <div class="highlight-icon bg-light mr-3">
            <i class="mdi mdi-trophy-award text-primary icon-lg"></i>
            </div>
            <div class="wrapper">
              <p class="card-text mb-0 theme-color">Total Challenges</p>
              <div class="fluid-container">
                <h3 class="card-title mb-0">{{$data['challenge']}}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="card-title mb-0 theme-color">Build Wins</h5>
            <small class="text-gray d-none d-sm-block"></small>
          </div>
          <div class="new-accounts">
            <ul class="chats">
              @if(!empty($data['topFiveWinBuilds']))
              @foreach($data['topFiveWinBuilds'] as $wbuild)
              <li class="chat-persons">
                <a href="{{url('builds/'.$wbuild->id)}}">
                  <span class="pro-pic">
                  <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$wbuild->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                  </span>
                  <div class="user">
                    <p class="u-name">{{$wbuild->build_text}}</p>
                    <p class="u-designation"><b>{{$wbuild->full_name}}</b></p>
                  </div>
                  <p class="joined-date"><label class="badge badge-danger"><b>{{$wbuild->count}}</b></label></p>
                </a>
              </li>
              @endforeach
              @else
              <li class="chat-persons">
                <span>No Record Found</span>
              </li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="card-title mb-0 theme-color">Build Losses</h5>
            <small class="text-gray d-none d-sm-block"></small>
          </div>
          <div class="new-accounts">
            <ul class="chats">
              @if(!empty($data['topFiveLoseBuilds']))
              @foreach($data['topFiveLoseBuilds'] as $lbuild)
              <li class="chat-persons">
                <a href="{{url('builds/'.$lbuild->id)}}">
                  <span class="pro-pic">
                  <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$lbuild->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                  </span>
                  <div class="user">
                    <p class="u-name">{{$lbuild->build_text}}</p>
                    <p class="u-designation"><b>{{$lbuild->full_name}}</b></p>
                  </div>
                  <p class="joined-date"><label class="badge badge-danger"><b>{{$lbuild->count}}</b></label></p>
                </a>
              </li>
              @endforeach
              @else
              <li class="chat-persons">
                <span>No Record Found</span>
              </li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="card-title mb-0 theme-color">Active Builds</h5>
            <small class="text-gray d-none d-sm-block"></small>
          </div>
          <div class="new-accounts">
            <ul class="chats">
              @if(!empty($data['topFiveCurrentBuilds']))
              @foreach($data['topFiveCurrentBuilds'] as $cbuild)
              <li class="chat-persons">
                <a href="{{url('builds/'.$cbuild->id)}}">
                  <span class="pro-pic">
                  <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$cbuild->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                  </span>
                  <div class="user">
                    <p class="u-name">{{$cbuild->build_text}}</p>
                    <p class="u-designation"><b>{{$cbuild->full_name}}</b></p>
                  </div>
                </a>
              </li>
              @endforeach
              @else
              <li class="chat-persons">
                <span>No Record Found</span>
              </li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="card-title mb-0 theme-color">Active Challenges</h5>
            <small class="text-gray d-none d-sm-block"></small>
          </div>
          <div class="new-accounts">
            <ul class="chats">
              @if(!empty($data['fiveActiveChallanges']))
              @foreach($data['fiveActiveChallanges'] as $challenge)
              <li class="chat-persons">
                <a href="{{url('challenge/'.$challenge['id'])}}">
                  <span class="pro-pic">
                  <img src="<?php echo \Storage::disk('s3')->url('images/challenge/'.$challenge['image']);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                  </span>
                  <div class="user">
                    <p class="u-name">{{$challenge['challenge_text']}}</p>
                  </div>
                </a>
              </li>
              @endforeach
              @else
              <li class="chat-persons">
                <span>No Record Found</span>
              </li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@endsection