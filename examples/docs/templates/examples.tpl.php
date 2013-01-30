<h2>Documentation</h2>
<p>The {{:sitename}} documentation is written using the framework as an example of how to use the framework for simple content pages. It shows how to have a layout with navigation, common headers and footers that load on every page, and how to create a layout that returns the content in json format.<br />
It also shows how each route can be treated as a module. Multiple modules can be specified in the URL and all will be loaded and displayed within the layout.
</p>

<h2>Quickstart</h2>
<p>Not truly an example, but a directory that can be copied to jumpstart a new project. All the base setup code is in place, but minimal functionality.</p>


<h2>To Do List</h2>
<p>This example utilizes the database abstraction class and requires a database and tables to be created in order to work. It shows many of the key features of the database abstraction class like profiling and bulk inserts. The example also shows more dynamic functionality by interacting with the user. A single template, multiple data is used to display the task list in various states. Messaging functionality is also implemented.</p>


<h2>REST</h2>
<p>An example of how to implement a RESTful interface for a website. Each route can specify which REST method(s) it supports and a hook will enforce whether the route can be run or not. It also shows how routes can be divided into different files so only the potentially valid route are loaded.</p>