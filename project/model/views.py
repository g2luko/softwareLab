from django.shortcuts import render

from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login
from django.contrib import messages
from .models import store

def login_view(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        user = authenticate(request, username=username, password=password)
        if user is not None:
            login(request, user)
            messages.info(request, f"You are now logged in as {username}.")
            return redirect('login')  # Replace 'home' with your desired redirect URL
        else:
            messages.error(request, "Invalid username or password.")
    return render(request, 'login.html')

from django.contrib.auth.models import User

def signup_view(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        password_confirm = request.POST.get('password_confirm')

        if password == password_confirm:
            if User.objects.filter(username=username).exists():
                messages.error(request, "Username already taken.")
            else:
                user = User.objects.create_user(username=username, password=password)
                user.save()
                login(request, user)
                messages.success(request, f"Account created for {username}.")
                return redirect('signup')  # Replace 'home' with your desired redirect URL
        else:
            messages.error(request, "Passwords do not match.")
    return render(request, 'signup.html')

from django.http import HttpResponse
def home(request):
    return render(request, 'home.html')
def data(request):
    # obj = User.objects.all()
    obj = store.objects.all()
    return render(request, 'data.html', {'obj': obj})