<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Tokbuk | @yield('title')</title>
  <script>
    window.BASEURL = "{{ url('/') }}";
    // window.pusher = {
    //   appKey: "{{ config('app.pusher.app_key') }}"
    // };
  </script>
  <link rel="icon" type="image/png" sizes="32x32" href="{{asset('assets/img/favicon.png')}}">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <!-- csrf-token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- jquery -->

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <!-- Preload critical resources -->
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- DataTables-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.css" />
  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Template CSS -->
  <link rel="stylesheet" href={{asset('assets/css/style.css')}}>
  <link rel="stylesheet" href={{asset('assets/css/components.css')}}>

  <style>
    .select2-selection__rendered { display: flex !important; align-items: center !important; }

    /* Ensure Font Awesome icons are displayed */
    .fas, .far, .fab, .fal, .fad, .fa {
      font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome" !important;
      -webkit-font-smoothing: antialiased;
      display: inline-block;
      font-style: normal;
      font-variant: normal;
      text-rendering: auto;
      line-height: 1;
    }

    .fas, .fa {
      font-weight: 900;
    }

    .far {
      font-weight: 400;
    }
  </style>


</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      @include('layouts.partials.navbar')
      @include('layouts.partials.sidebar')
      <div class="content-wrapper">
        @yield('content')
      </div>
      @include('layouts.partials.footer')
    </div>
  </div>
  <!-- General JS Scripts -->
  {{-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script> --}}

  <script>
    // const pusher = new Pusher(window.pusher.appKey, {
    //   cluster: 'ap1'
    // });

    // const channelBind = (channelName, event, callback) => {
    //   const channel = pusher.subscribe(channelName);
    //   channel.bind(event, callback);
    // }
  </script>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src={{asset('assets/js/stisla.js')}}></script>
  <!-- Template JS File -->
  <script src={{asset('assets/js/scripts.js')}}></script>
  <script src={{asset('assets/js/custom.js')}}></script>
  <!-- Page Specific JS File -->
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/datatables.min.js"></script>
  <script src={{asset('assets/js/sweetalert2.all.min.js')}}></script>
  {{-- <script src="{{asset('js/style.js')}}"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
  <script src={{asset('js/helpers.js')}}></script>

  <script>
    // $('form').submit(function() {
    //   $button = $(this).find('button[type=submit]');
    //   $button.prop('disabled', true);

    //   switch ( $button.text() ) {
    //     case 'Simpan' : $button.text('Menyimpan...'); break;
    //     case 'Simpan Perubahan' : $button.text('Menyimpan Perubahan...'); break;
    //     case 'Ganti Password' : $button.text('Mengganti Password...'); break;
    //     case 'Filter' : $button.text('Memfilter...'); break;
    //   }
    // });
  </script>

  @stack('js')
  @stack('css')

  <script>
    $(function () {
        $("#table-index").DataTable();
    });

  </script>

  <script>
    $(function(){
      function deleteWithForm($form) {
        Swal.fire({
          title: 'Apakah anda yakin ingin menghapus data tersebut?',
          text: "Data tersebut kemungkinan berhubungan dengan data lainnya, pastikan anda benar-benar yakin ingin menghapus data tersebut.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Hapus!'
        })
        .then((result) => {
          if (result.value) {
            $form.submit();
          }
        });
      }

      $(document).on('click', function(event) {
        if ( (event.target.tagName === 'BUTTON' && $(event.target).parent().hasClass('delete_form')) ) {
          event.preventDefault();
          deleteWithForm($(event.target).parent());
          return;
        }

        if ( (event.target.tagName === 'I' && $(event.target).parent().hasClass('btn-delete')) ) {
          event.preventDefault();
          deleteWithForm($(event.target).parent().parent());
          return;
        }
      });

      $('[data-tooltip="tooltip"]').tooltip();
      $('select:not(.no-select2)').select2();
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

  @yield('js')

  <input type="text" id="copy" hidden>
</body>

</html>
