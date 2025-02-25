from flask import Flask, render_template, request, redirect, url_for, session, flash

app = Flask(__name__)
app.secret_key = 'your_secret_key_here'  # Replace with a secure key in production

# Mock database for demonstration purposes
users = {}
messages = []

@app.route('/')
def index():
    if 'username' in session:
        return redirect(url_for('chat'))
    return render_template('index.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        if username in users and users[username] == password:
            session['username'] = username
            flash('Login successful!', 'success')
            return redirect(url_for('chat'))
        else:
            flash('Invalid username or password.', 'danger')
    return render_template('login.html')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        if username in users:
            flash('Username already exists.', 'danger')
        else:
            users[username] = password
            flash('Registration successful! Please log in.', 'success')
            return redirect(url_for('login'))
    return render_template('register.html')

@app.route('/logout')
def logout():
    session.pop('username', None)
    flash('You have been logged out.', 'info')
    return redirect(url_for('index'))

@app.route('/chat', methods=['GET', 'POST'])
def chat():
    if 'username' not in session:
        flash('You need to log in to access the chat.', 'warning')
        return redirect(url_for('login'))

    if request.method == 'POST':
        message = request.form['message']
        if message:
            messages.append({'user': session['username'], 'message': message})
            flash('Message sent!', 'success')

    return render_template('chat.html', messages=messages)

if __name__ == '__main__':
    app.run(debug=True)
