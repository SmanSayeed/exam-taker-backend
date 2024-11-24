<div class="container " style="margin-bottom: -35px;margin-top: 35px;">
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-medical-tab" data-bs-toggle="pill" data-bs-target="#pills-medical" type="button" role="tab" aria-controls="pills-medical" aria-selected="true">Medical</button>
        </li>
        <!--<li class="nav-item" role="presentation">-->
        <!--  <button class="nav-link" id="pills-varsity-tab" data-bs-toggle="pill" data-bs-target="#pills-varsity" type="button" role="tab" aria-controls="pills-varsity" aria-selected="false">Varsity</button>-->
        <!--</li>-->
      </ul>
 </div>


 <div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-medical" role="tabpanel" aria-labelledby="pills-medical-tab">
        @include('pages.pageComponents.courses.medical-course-cards')
    </div>
    <div class="tab-pane fade" id="pills-varsity" role="tabpanel" aria-labelledby="pills-varsity-tab">
        @include('pages.pageComponents.courses.varsity-course-cards')
    </div>

  </div>
