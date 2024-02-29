@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.header')
@else
    @include('common.store_admin.header')
@endif    
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Product Reviews</h2>
        </div>
    </div>
    <div class="card mb-4">
        <header class="card-header">
            <div class="row gx-3">
                <div class="col-lg-3 col-md-5 col-6">
                    <select class="form-select">
                        <option>Filter By Rating</option>
                        <option>Rating (High > Low)</option>
                        <option>Rating (Low > High)</option>
                        
                    </select>
                </div>
            </div>
        </header>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="example">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                </div>
                            </th>
                            <th>#ID</th>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th class="text-end">Published</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                </div>
                            </td>
                            <td>23</td>
                            <td><b>Product name</b></td>
                            <td>Devon Lane</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 60%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" >									  
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                </div>
                            </td>
                            <td>24</td>
                            <td><b>Product name</b></td>
                            <td>Guy Hawkins</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 80%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" >									  
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked value="">
                                </div>
                            </td>
                            <td>25</td>
                            <td><b>Product name</b></td>
                            <td>Steven John</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 90%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" checked role="switch" >									  
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                </div>
                            </td>
                            <td>26</td>
                            <td><b>Product name</b></td>
                            <td>Kristin Watson</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 90%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" >									  
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  value="">
                                </div>
                            </td>
                            <td>27</td>
                            <td><b>Product name</b></td>
                            <td>Jane Cooper</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 100%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" checked role="switch" >									  
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="">
                                </div>
                            </td>
                            <td>28</td>
                            <td><b>Product name</b></td>
                            <td>Courtney Henry</td>
                            <td>
                                <ul class="rating-stars">
                                    <li style="width: 100%" class="stars-active">
                                        <img src="assets/imgs/icons/stars-active.svg" alt="stars">
                                    </li>
                                    <li>
                                        <img src="assets/imgs/icons/starts-disable.svg" alt="stars">
                                    </li>
                                </ul>
                            </td>
                            <td>I am Happy .... <a href="#">More</a></td>
                            <td class="text-end">
                                <div class="form-check form-switch ps-0">									
                                    <input class="form-check-input ms-0" type="checkbox" role="switch" >									  
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section> 
@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.footer')
@else
    @include('common.store_admin.footer')
@endif 