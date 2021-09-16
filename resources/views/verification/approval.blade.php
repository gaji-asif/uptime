<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v3.8.5">
  <title>UptimeProfile - Verify Submission</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }

    #btn-next, #btn-complete, #txt-complete {
      display: none;
    }
  </style>
  <!-- Custom styles for this template -->
  <link href="{{asset('css/verification.css')}}" rel="stylesheet">

</head>
<body class="text-center">
  <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="masthead mb-auto">
      <div class="inner">

        <h6 class="masthead-brand">
          <div>
            <img src="{{asset('images/uptime-logo.png')}}" alt=""><br>
            Your Uptime Verified
            <br>
            <br>
          </div>
        </h6>
      </div>
    </header>

    <main role="main" class="inner cover">
      <form action="{{ route('verify.submission.process', ['uuid'=>$uuid]) }}" method="POST" id="approval_form">
      @csrf
      <div class="container content text-left">
        <div class="row justify-content-center">
          @foreach ($builds as $build)
            <div class="col-lg-6 col-md-6 col-sm-12 bgphoto" data-build="{{$loop->iteration}}">
              <div class="photo" style="background-image: url(<?php echo \Storage::disk('s3')->url('images/build/'.$build['image']); ?>);">
                <a class="btn btn-light glightbox" href="<?php echo \Storage::disk('s3')->url('images/build/'.$build['image']); ?>" data-gallery="gallery-{{$loop->iteration}}">
                  <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          @endforeach
          
          <div class="col-lg-4 col-md-6 col-sm-12 sidebar">
            <div class="container-fluid d-flex h-100">
              <div class="row">
                <div class="col-12">
                  <br>
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach ($builds as $build)
                    <li class="nav-item">
                      <a class="nav-link <?php echo $loop->iteration != 1 ? "" : "active"; ?>" id="build-{{$loop->iteration}}-tab" data-toggle="tab" href="#build-{{$loop->iteration}}" role="tab" aria-selected="true" data-build="{{$loop->iteration}}">
                        <img src="<?php echo \Storage::disk('s3')->url('images/build/'.$build['image']); ?>" width="50" height="50" style="object-fit: cover" alt="">
                      </a>
                    </li>
                    @endforeach
                  </ul>
                  <div class="tab-content " id="myTabContent">
                    @foreach ($builds as $build)
                    <div class="tab-pane <?php echo $loop->iteration != 1 ? "fade" : "show active"; ?>" id="build-{{$loop->iteration}}" role="tabpanel">
                      <div class="row">
                        <div class="col p-4">
                          <figure class="quote">
                              <div style="font-size: 13px"> Photo Description: </div>
                              <p> {{ $build['build_text'] }} </p>
                            <div>
                              Category:

                                <span class="badge badge-info">{{ $build['category_name'] }}</span>

                            </div>
                            <figcaption class="quote-by">
                              — {{ $user_data['full_name'] }}
                              @if($user_data['image'] != '')
                                <img 
                                    src="<?php echo $user_data['image'];?>" 
                                    alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                              @else
                                <img src="{{ asset('images/avatar.png') }}" alt="Profile image">
                              @endif
                            </figcaption>
                          </figure>
                        </div>
                      </div>
                      <div class="row justify-content-between">
                        <div class="col-6">
                          <label class="btn btn-danger btn-block btn-sm">
                            <input type="radio" class="reject processed" data-build="{{$loop->iteration}}" name="build_{{$build->id}}" value="reject" autocomplete="off"> <span class="process_label">Reject</span>
                          </label>
                        </div>
                        <div class="col-6">
                          <label class="btn btn-success btn-block btn-sm">
                            <input type="radio" class="approve processed" data-build="{{$loop->iteration}}" name="build_{{$build->id}}" value="approve" autocomplete="off"> <span class="process_label">Approve</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                  <br>
                  <button type="button" class="btn btn-block btn-info" id="btn-next">
                    NEXT
                  </button>
                </div>
                <div class="col-12 mt-auto">
                  <div>
                    <p class="text-center text-muted small" id="txt-complete">
                      Please click the button below once all photos have been reviewed
                    </p>
                    <button class="btn btn-block btn-primary" type="button" id="btn-complete" data-toggle="modal" data-target="#disclaimerModel">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      COMPLETE VERIFICATION
                    </button>
                    <button class="submit btn btn-block btn-primary" style="display: none;" id="submit_complete">
                      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      COMPLETE VERIFICATION
                    </button>
                  </div>
                  <div>
                    <br>
                    <h6 class="text-muted">Reviewed &amp; approved by</h6>
                    <h4 style="font-family: 'Caveat', cursive; font-size: 35px;">{{$verifier_name}}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </main>

    <footer class="mastfoot mt-auto">
      <div class="inner">
        <br>
        <p>&copy; {{ date("Y") }} by <a href="https://uptimeprofile.com/">UptimeProfile.com</a>.</p>
      </div>
    </footer>
  </div>

  <!-- Modal -->
<div class="modal fade text-left" id="disclaimerModel" tabindex="-1" role="dialog" aria-labelledby="disclaimerModelTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Finalize</h5>
      </div>
      <div class="modal-body">
        <p>
          By continuing you have approved and/or rejected {{ $user_data['full_name'] }}'s submissions as a verified reference.
        </p>
        <br>
        

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="agree-btn">Confirm</button>
      </div>
    </div>
  </div>
</div>
</div>

  <!-- stylesheets -->
  <link href="https://fonts.googleapis.com/css?family=Caveat&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

  <!-- javascript -->
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script>
  $(function () {

    // var disclaimerModel = document.getElementById('disclaimerModel');
    // disclaimerModel.style.display = "none";



    $('#agree-btn').click(function(e) {
       $('#submit_complete').click();
    });

    var count = {{count($builds)}};
    var index = 1;
    $('.bgphoto[data-build=1]').show();
    const lightbox = GLightbox({
      touchNavigation: true,
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      // console.log(e.target) // newly activated tab
      // console.log(e.relatedTarget) // previous active tab
      $('.bgphoto[data-build='+$(e.relatedTarget).data('build')+']').hide();
      $('.bgphoto[data-build='+$(e.target).data('build')+']').show();
      index = parseInt($(e.target).data('build'));
      var items = $('#approval_form').find('input[name*=build_]:checked');
      if (items.length < count) {
        items = $('#approval_form').find('input[data-build='+(index.toString())+']:checked');
        if (items.length == 0) {
          $('#btn-next').hide();
        } else {
          $('#btn-next').show();
        }
      }
    });
    $('input.processed').click(function(e) {
      if ($(e.target).hasClass('approve')) {
        $(e.target).parent('label').find('.process_label').html('Approved');
        $(e.target).parent('label').parent('div').parent('.row').find('.btn-danger .process_label').html('Reject');
        $('#myTab li:nth-child('+(index.toString())+')').attr('class', 'nav-item approved');
      } else {
        $(e.target).parent('label').find('.process_label').html('Rejected');
        $(e.target).parent('label').parent('div').parent('.row').find('.btn-success .process_label').html('Approve');
        $('#myTab li:nth-child('+(index.toString())+')').attr('class', 'nav-item rejected');
      }

      var items = $('#approval_form').find('input[name*=build_]:checked');

      if (items.length == count) {
        $('#txt-complete').show();
        $('#btn-complete').show();
        $('#btn-next').hide();
      } else {
        $('#btn-next').show();
      }
    });
    $('#approval_form').on( "submit", function(e) {
      $('#btn-complete').attr('disabled', true);
    });
    $('#btn-next').click(function(e) {
      if (index < count)
        index++;
      else
        index = 1;
      $('#myTab li:nth-child('+(index.toString())+') a').tab('show');
      var items = $('#approval_form').find('input[data-build='+(index.toString())+']:checked');
      if (items.length == 0) {
        $('#btn-next').hide();
      }
    });
  });
    
  </script>
</body>
</html>


