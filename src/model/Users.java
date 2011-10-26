package model;

import java.io.Serializable;

import java.lang.StringBuilder;

import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;

import javax.xml.bind.annotation.*;

import javax.persistence.*;

/**
 */

@Entity
@NamedQueries({
		@NamedQuery(name = "findAllUserss", query = "select myUsers from Users myUsers"),
		@NamedQuery(name = "findUsersById", query = "select myUsers from Users myUsers where myUsers.id = ?1"),
		@NamedQuery(name = "findUsersByName", query = "select myUsers from Users myUsers where myUsers.name = ?1"),
		@NamedQuery(name = "findUsersByNameContaining", query = "select myUsers from Users myUsers where myUsers.name like ?1"),
		@NamedQuery(name = "findUsersByPassword", query = "select myUsers from Users myUsers where myUsers.password = ?1"),
		@NamedQuery(name = "findUsersByPasswordContaining", query = "select myUsers from Users myUsers where myUsers.password like ?1"),
		@NamedQuery(name = "findUsersByPrimaryKey", query = "select myUsers from Users myUsers where myUsers.id = ?1"),
		@NamedQuery(name = "findUsersByType", query = "select myUsers from Users myUsers where myUsers.type = ?1") })
@Table(catalog = "HACKATHON", name = "users")
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(namespace = "Hackathon/com/model", name = "Users")
public class Users implements Serializable {
	private static final long serialVersionUID = 1L;

	/**
	 */

	@Column(name = "id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@Id
	@XmlElement
	Integer id;
	/**
	 */

	@Column(name = "name", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String name;
	/**
	 */

	@Column(name = "type", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer type;
	/**
	 */

	@Column(name = "password", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String password;

	/**
	 */
	public void setId(Integer id) {
		this.id = id;
	}

	/**
	 */
	public Integer getId() {
		return this.id;
	}

	/**
	 */
	public void setName(String name) {
		this.name = name;
	}

	/**
	 */
	public String getName() {
		return this.name;
	}

	/**
	 */
	public void setType(Integer type) {
		this.type = type;
	}

	/**
	 */
	public Integer getType() {
		return this.type;
	}

	/**
	 */
	public void setPassword(String password) {
		this.password = password;
	}

	/**
	 */
	public String getPassword() {
		return this.password;
	}

	/**
	 */
	public Users() {
	}

	/**
	 * Copies the contents of the specified bean into this bean.
	 *
	 */
	public void copy(Users that) {
		setId(that.getId());
		setName(that.getName());
		setType(that.getType());
		setPassword(that.getPassword());
	}

	/**
	 * Returns a textual representation of a bean.
	 *
	 */
	public String toString() {

		StringBuilder buffer = new StringBuilder();

		buffer.append("id=[").append(id).append("] ");
		buffer.append("name=[").append(name).append("] ");
		buffer.append("type=[").append(type).append("] ");
		buffer.append("password=[").append(password).append("] ");

		return buffer.toString();
	}

	/**
	 */
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = (int) (prime * result + ((id == null) ? 0 : id.hashCode()));
		return result;
	}

	/**
	 */
	public boolean equals(Object obj) {
		if (obj == this)
			return true;
		if (!(obj instanceof Users))
			return false;
		Users equalCheck = (Users) obj;
		if ((id == null && equalCheck.id != null) || (id != null && equalCheck.id == null))
			return false;
		if (id != null && !id.equals(equalCheck.id))
			return false;
		return true;
	}
}
