<!-- Footer Nav -->
<div class="footer-nav-area" id="footerNav">
    <div class="container px-0">
        <!-- Footer Content -->
        <div class="footer-nav position-relative">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                </li>

                <li class="{{ request()->is('question-bank*') ? 'active' : '' }}">
                    <a href="elements.html">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Question Bank</span>
                    </a>
                </li>

                <li class="{{ request()->is('practice*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <span>Practice</span>
                    </a>
                </li>

                <li class="{{ request()->is('courses*') ? 'active' : '' }}">
                    <a href={{route('courses')}}>
                        <i class="fa-solid fa-person-chalkboard"></i>
                        <span>Course</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
