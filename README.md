<h2>Overview</h2>
Basecoat is designed to cover the basics: MVC, Front Controller, Templating, and Database abstraction. It is designed to be "included" in your code and using it as a full framework is optional. This allows one to build parts of a website in the framework and migrate over time. It does not enforce a coding style, naming convention, or have many dependencies. Basecoat is designed to be a centralized core code base that websites are built on. The same core code base can be loaded by many different web sites and/or applications. The configurations that dictate the framework's behavior are part of the website code, not the framework. Pulling in updates won't overwrite any of your own code. 

The entire framework is about 5 files and 2,000 lines of code. You should be able start creating a web site within a few hours (possibly 1 hour) of downloading the framework. Performance is quite good, handling hundreds of pages per second, even without accelerators like APC. It requires no special setup or configuration, not even mod_rewrite. It can be configured to use parameter based URLs or if you want "pretty urls", mod_rewrite or something similar.

<h2>Documentation</h2>
To get started, simply download the framework, including the examples. Place the whole framework in a web accessible directory and point your browser at basecoat/examples/docs/public/. The documentation is written using the framework, so you can view the code and see how it is working while you read through the documentation. Typically you would only have the public directory web accessible, but that is up to you.

Once you are comfortable with the setup, you can move the framework directory to a centralized location that is not web accessible.

<h2>Quickstart</h2>
There is also a quickstart directory in the example directory that can be used as a template for creating new websites. Simply make a copy of the quickstart directory and modify it to fit the needs to your new website.
<ol>
<li>Make a copy of the quickstart directory and put it wherever you want your website</li>
<li>Edit the public/index.php to load the basecoat.php</li>
</ol>
Load your new site by pointing your browser at the public directory of your new site.

<h2>License</h2>
Copyright (C) 2011-2013 by Shutterstock Images, LLC

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
