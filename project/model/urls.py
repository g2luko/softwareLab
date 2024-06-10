from django.urls import path
from . import views

urlpatterns = [
    path('login/', views.login_view, name='login'),
    path('signup/', views.signup_view, name='signup'),  
    path("", views.home, name="home"),
    path("data/", views.data, name="data")
]
